<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Reconciles variation option prices against the menu sheet.
 *
 * Customers pay food.price + option_price (see Helpers::get_varient and the
 * Flutter item_details_screen), so the cheapest option must sit at +0 and the
 * base must carry the cheapest absolute price. Where that invariant was broken
 * the base was charged twice - e.g. بيتزا سجق billed 360 for a 230 pizza.
 *
 * For each variation group: take the cheapest sheet price as the new base,
 * then set every option_price to (sheet price - base).
 */
class SyncVariationPrices extends Command
{
    protected $signature = 'menu:sync-variations
                            {--restaurant=1 : Restaurant id to scope to}
                            {--csv= : Local CSV instead of fetching the sheet}
                            {--apply : Write changes (default is a dry run)}';

    protected $description = 'Sync variation option prices from the menu sheet, keeping the cheapest option at +0';

    private const SHEET_URL = 'https://docs.google.com/spreadsheets/d/1Sv5x2MLn9ptOlFGK8fCu4r8DurQdPJYVWGq4g_mJpmM/export?format=csv';

    /** Option label spellings -> canonical form used for matching. */
    private const LABEL_ALIASES = [
        '1 كيلو' => 'كيلو', '1كيلو' => 'كيلو', 'كيلو' => 'كيلو',
        '1/4 كيلو' => '1/4 ك', '1/4 ك' => '1/4 ك', 'ربع كيلو' => '1/4 ك',
        '1/2 كيلو' => '1/2 ك', '1/2 ك' => '1/2 ك', '1/2ك' => '1/2 ك', 'نص كيلو' => '1/2 ك',
        // Live pizzas offer صغير/وسط; the sheet writes the same two sizes as
        // صغيرة/كبيرة. Fold both vocabularies onto small/large.
        'صغيرة' => 'صغير', 'صغير' => 'صغير',
        'كبيرة' => 'كبير', 'كبير' => 'كبير', 'وسط' => 'كبير',
        'خبز سياحي' => 'سياحي', 'سياحى' => 'سياحي', 'سياحي' => 'سياحي',
        'خبز كيزر' => 'كايزر', 'كيزر' => 'كايزر', 'كايزر' => 'كايزر',
        'خبز صاج' => 'صاج', 'صاج' => 'صاج',
        'خبز فينو' => 'فينو', 'فينو' => 'فينو',
    ];

    /**
     * DB dish names carry a trailing مشوي/مشوية that the sheet omits
     * (كفتة فراخ مشوية vs كفتة فراخ). Strip it for matching only.
     */
    private function dishKey(string $s): string
    {
        return trim(preg_replace('/\s*(مشوي|مشويه|مشوى|مشويا)$/u', '', $this->normalize($s)));
    }

    private function normalize(string $s): string
    {
        $s = trim($s);
        $s = preg_replace('/[\x{064B}-\x{0652}\x{0640}]/u', '', $s);
        $s = preg_replace('/[\x{0622}\x{0623}\x{0625}\x{0671}]/u', "\u{0627}", $s);
        $s = str_replace(["\u{0629}", "\u{0649}", "\u{0624}", "\u{0626}"],
                         ["\u{0647}", "\u{064A}", "\u{0648}", "\u{064A}"], $s);
        return trim(preg_replace('/\s+/u', ' ', $s));
    }

    private function canonLabel(string $s): string
    {
        $n = $this->normalize($s);
        foreach (self::LABEL_ALIASES as $from => $to) {
            if ($n === $this->normalize($from)) {
                return $this->normalize($to);
            }
        }
        return $n;
    }

    /** Split a sheet row name into [baseName, optionLabel] or [name, null]. */
    private function split(string $name): array
    {
        $n = $this->normalize($name);
        foreach (array_keys(self::LABEL_ALIASES) as $token) {
            $t = $this->normalize($token);
            if ($t === '') {
                continue;
            }
            if (str_starts_with($n, $t.' ')) {
                return [trim(substr($n, strlen($t))), $this->canonLabel($token)];
            }
            if (str_ends_with($n, ' '.$t) || str_ends_with($n, $t)) {
                return [trim(substr($n, 0, strlen($n) - strlen($t))), $this->canonLabel($token)];
            }
        }
        return [$n, null];
    }

    private function loadSheet(): array
    {
        $path = $this->option('csv');
        $raw = $path ? @file_get_contents($path) : @file_get_contents(self::SHEET_URL);
        if ($raw === false || trim($raw) === '') {
            $this->error('Could not read menu CSV.');
            return [];
        }
        $raw = preg_replace('/^\x{FEFF}/u', '', $raw);
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        array_shift($lines);

        $out = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            $c = str_getcsv($line);
            $name = trim($c[1] ?? '');
            $price = trim($c[3] ?? '');
            if ($name === '' || !is_numeric($price) || (float) $price <= 0) {
                continue;
            }
            $out[] = ['name' => $name, 'price' => (float) $price];
        }
        return $out;
    }

    public function handle(): int
    {
        $restaurantId = (int) $this->option('restaurant');
        $apply = (bool) $this->option('apply');

        $sheet = $this->loadSheet();
        if (!$sheet) {
            return self::FAILURE;
        }

        // sheet: base name -> [canonical label => price]
        $wanted = [];
        foreach ($sheet as $row) {
            [$base, $label] = $this->split($row['name']);
            if ($label === null) {
                continue;
            }
            $wanted[$this->dishKey($base)][$label] = $row['price'];
        }

        $groups = DB::table('variations as v')
            ->join('food as f', 'f.id', '=', 'v.food_id')
            ->where('f.restaurant_id', $restaurantId)
            ->where('f.status', 1)
            ->select('v.id as var_id', 'v.name as grp', 'f.id as food_id', 'f.name as food', 'f.price as base')
            ->get();

        $plannedFood = [];   // food_id => new base
        $plannedOpts = [];   // opt_id  => [name, old, new]
        $skipped = [];

        foreach ($groups as $g) {
            $key = $this->dishKey($g->food);

            if (!isset($wanted[$key])) {
                $skipped[] = [$g, 'no sheet rows for this dish'];
                continue;
            }

            $opts = DB::table('variation_options')
                ->where('variation_id', $g->var_id)
                ->get(['id', 'option_name', 'option_price']);

            $target = [];
            $missing = [];
            foreach ($opts as $o) {
                $lab = $this->canonLabel($o->option_name);
                if (!isset($wanted[$key][$lab])) {
                    $missing[] = $o->option_name;
                    continue;
                }
                $target[$o->id] = ['label' => $o->option_name, 'abs' => $wanted[$key][$lab], 'old' => (float) $o->option_price];
            }

            if ($missing) {
                $skipped[] = [$g, 'options not in sheet: '.implode(', ', $missing)];
                continue;
            }
            if (count($target) < 2) {
                $skipped[] = [$g, 'fewer than 2 matched options'];
                continue;
            }

            $newBase = min(array_column($target, 'abs'));
            if ($newBase <= 0) {
                $skipped[] = [$g, 'computed base <= 0'];
                continue;
            }

            $plannedFood[$g->food_id] = ['name' => $g->food, 'old' => (float) $g->base, 'new' => $newBase];
            foreach ($target as $optId => $t) {
                $plannedOpts[$optId] = [
                    'food' => $g->food,
                    'label' => $t['label'],
                    'old' => $t['old'],
                    'new' => $t['abs'] - $newBase,
                    'charged_before' => (float) $g->base + $t['old'],
                    'charged_after' => $t['abs'],
                ];
            }
        }

        $this->info('Variation groups examined: '.$groups->count());
        $this->newLine();

        foreach ($plannedFood as $fid => $f) {
            $this->line(sprintf('<comment>#%d %s</comment>  base %.2f -> %.2f', $fid, $f['name'], $f['old'], $f['new']));
            foreach ($plannedOpts as $oid => $o) {
                if ($o['food'] !== $f['name']) {
                    continue;
                }
                $flag = abs($o['charged_before'] - $o['charged_after']) < 0.01 ? '' : '   <== customer price changes';
                $this->line(sprintf('    %-12s delta %8.2f -> %8.2f   customer pays %8.2f -> %8.2f%s',
                    $o['label'], $o['old'], $o['new'], $o['charged_before'], $o['charged_after'], $flag));
            }
        }

        if ($skipped) {
            $this->newLine();
            $this->line('<comment>Skipped groups: '.count($skipped).'</comment>');
            foreach ($skipped as [$g, $why]) {
                $this->line(sprintf('  #%d %s [%s] - %s', $g->food_id, $g->food, $g->grp, $why));
            }
        }

        $this->newLine();
        $this->info(sprintf('Summary: %d foods rebased, %d options updated, %d groups skipped.',
            count($plannedFood), count($plannedOpts), count($skipped)));

        if (!$apply) {
            $this->newLine();
            $this->warn('DRY RUN - nothing written. Re-run with --apply to commit.');
            return self::SUCCESS;
        }

        foreach ($plannedOpts as $oid => $o) {
            if ($o['new'] < 0) {
                $this->error('Refusing to write: negative delta on option '.$oid.' ('.$o['label'].').');
                return self::FAILURE;
            }
        }

        DB::beginTransaction();
        try {
            foreach ($plannedFood as $fid => $f) {
                DB::table('food')->where('id', $fid)->update(['price' => $f['new'], 'updated_at' => now()]);
            }
            foreach ($plannedOpts as $oid => $o) {
                DB::table('variation_options')->where('id', $oid)->update(['option_price' => $o['new'], 'updated_at' => now()]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Rolled back, nothing written: '.$e->getMessage());
            return self::FAILURE;
        }

        $this->info('Applied.');
        return self::SUCCESS;
    }
}
