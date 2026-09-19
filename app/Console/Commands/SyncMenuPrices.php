<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncMenuPrices extends Command
{
    protected $signature = 'menu:sync-prices
                            {--restaurant=1 : Restaurant id to scope updates to}
                            {--csv= : Path to a local CSV instead of fetching the sheet}
                            {--apply : Actually write changes (default is dry run)}';

    protected $description = 'Sync food prices from the Beit Jedi menu sheet. Updates price only; never creates or deletes rows.';

    private const SHEET_URL = 'https://docs.google.com/spreadsheets/d/1Sv5x2MLn9ptOlFGK8fCu4r8DurQdPJYVWGq4g_mJpmM/export?format=csv';

    /**
     * Arabic names vary in orthography between the sheet and the DB
     * (أ/إ/آ vs ا, ة vs ه, ى vs ي, diacritics, tatweel, double spaces).
     * Normalise both sides so those never count as different items.
     */
    private function normalize(string $s): string
    {
        $s = trim($s);
        $s = preg_replace('/[\x{064B}-\x{0652}\x{0640}]/u', '', $s); // harakat + tatweel
        $s = preg_replace('/[\x{0622}\x{0623}\x{0625}\x{0671}]/u', "\u{0627}", $s); // آأإٱ -> ا
        $s = str_replace(["\u{0629}", "\u{0649}", "\u{0624}", "\u{0626}"],
                         ["\u{0647}", "\u{064A}", "\u{0648}", "\u{064A}"], $s); // ة->ه ى->ي ؤ->و ئ->ي
        $s = preg_replace('/\s+/u', ' ', $s);
        return trim($s);
    }

    private function loadRows(): array
    {
        $path = $this->option('csv');
        $raw = $path ? @file_get_contents($path) : @file_get_contents(self::SHEET_URL);

        if ($raw === false || trim($raw) === '') {
            $this->error('Could not read menu CSV from '.($path ?: self::SHEET_URL));
            return [];
        }

        $raw = preg_replace('/^\x{FEFF}/u', '', $raw);
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        array_shift($lines); // header

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
            $out[] = ['name' => $name, 'price' => (float) $price, 'category' => trim($c[0] ?? '')];
        }
        return $out;
    }

    public function handle(): int
    {
        $restaurantId = (int) $this->option('restaurant');
        $apply = (bool) $this->option('apply');

        $items = $this->loadRows();
        if (!$items) {
            return self::FAILURE;
        }
        $this->info('Sheet items: '.count($items));

        $foods = DB::table('food')
            ->where('restaurant_id', $restaurantId)
            ->get(['id', 'name', 'price']);
        $this->info('Live foods (restaurant '.$restaurantId.'): '.$foods->count());

        // Normalised name -> rows. Duplicates are kept so we can report them
        // rather than silently updating an arbitrary one.
        $byName = [];
        foreach ($foods as $f) {
            $byName[$this->normalize($f->name)][] = $f;
        }

        $changes = $unchanged = $ambiguous = $missing = [];

        // The sheet itself can list the same name twice under different
        // categories at different prices (e.g. a sandwich and a platter).
        // Those must never be applied: two rows would target the same food
        // and the last write would silently win. Collect and skip them.
        $sheetCounts = [];
        foreach ($items as $item) {
            $sheetCounts[$this->normalize($item['name'])][] = $item;
        }
        $sheetDupes = array_filter($sheetCounts, fn ($g) => count($g) > 1);

        foreach ($items as $item) {
            $key = $this->normalize($item['name']);

            if (isset($sheetDupes[$key])) {
                continue; // reported separately below
            }

            if (!isset($byName[$key])) {
                $missing[] = $item;
                continue;
            }
            if (count($byName[$key]) > 1) {
                $ambiguous[] = ['item' => $item, 'rows' => $byName[$key]];
                continue;
            }

            $food = $byName[$key][0];
            if (abs((float) $food->price - $item['price']) < 0.01) {
                $unchanged[] = $item;
                continue;
            }
            $changes[] = ['id' => $food->id, 'name' => $food->name, 'old' => (float) $food->price, 'new' => $item['price']];
        }

        $this->newLine();
        $this->line('<comment>Price changes: '.count($changes).'</comment>');
        foreach ($changes as $c) {
            $this->line(sprintf('  #%-5d %-45s %8.2f -> %8.2f', $c['id'], mb_strimwidth($c['name'], 0, 45, '…'), $c['old'], $c['new']));
        }

        if ($sheetDupes) {
            $this->newLine();
            $this->line('<comment>Duplicate names IN THE SHEET (SKIPPED - ambiguous, fix the sheet): '.count($sheetDupes).'</comment>');
            foreach ($sheetDupes as $key => $group) {
                $detail = implode(' | ', array_map(
                    fn ($g) => sprintf('%.2f [%s]', $g['price'], $g['category'] ?: '-'),
                    $group
                ));
                $inDb = isset($byName[$key]) ? ' -> db#'.implode(',', array_map(fn ($r) => $r->id, $byName[$key])) : ' -> not in db';
                $this->line('  '.$group[0]['name'].'  '.$detail.$inDb);
            }
        }

        if ($ambiguous) {
            $this->newLine();
            $this->line('<comment>Ambiguous (duplicate names, SKIPPED): '.count($ambiguous).'</comment>');
            foreach ($ambiguous as $a) {
                $ids = implode(',', array_map(fn ($r) => $r->id, $a['rows']));
                $this->line('  '.$a['item']['name'].'  -> ids ['.$ids.']');
            }
        }

        if ($missing) {
            $this->newLine();
            $this->line('<comment>In sheet but not in DB (SKIPPED, nothing created): '.count($missing).'</comment>');
            foreach ($missing as $m) {
                $this->line(sprintf('  %-45s %8.2f  [%s]', mb_strimwidth($m['name'], 0, 45, '…'), $m['price'], $m['category']));
            }
        }

        $this->newLine();
        $this->info(sprintf('Summary: %d to change, %d already correct, %d sheet-duplicates, %d ambiguous, %d not in DB.',
            count($changes), count($unchanged), count($sheetDupes), count($ambiguous), count($missing)));

        if (!$apply) {
            $this->newLine();
            $this->warn('DRY RUN - nothing written. Re-run with --apply to commit these changes.');
            return self::SUCCESS;
        }

        if (!$changes) {
            $this->info('Nothing to write.');
            return self::SUCCESS;
        }

        // Belt and braces: never let two updates target the same row.
        $ids = array_column($changes, 'id');
        if (count($ids) !== count(array_unique($ids))) {
            $dupIds = array_unique(array_diff_assoc($ids, array_unique($ids)));
            $this->error('Refusing to write: food ids targeted more than once: '.implode(',', $dupIds));
            return self::FAILURE;
        }

        DB::beginTransaction();
        try {
            foreach ($changes as $c) {
                DB::table('food')
                    ->where('id', $c['id'])
                    ->where('restaurant_id', $restaurantId)
                    ->update(['price' => $c['new'], 'updated_at' => now()]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Rolled back, no changes written: '.$e->getMessage());
            return self::FAILURE;
        }

        $this->info('Applied '.count($changes).' price updates.');
        return self::SUCCESS;
    }
}
