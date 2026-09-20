<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Price sync for items whose app name differs from the sheet name.
 *
 * menu:sync-prices matches on the normalised name, so items carrying a
 * portion suffix (حمص بيروتي وسط), a redundant prefix (سلطه شمندر) or a
 * different spelling (حنيد لحمه vs حنيد لحم) never matched and kept stale
 * prices - six of them were genuinely mispriced, in both directions.
 *
 * Mappings are explicit rather than fuzzy: a wrong fuzzy match would reprice
 * the wrong dish silently. Each entry is food id => sheet item name.
 */
class SyncMenuAliases extends Command
{
    protected $signature = 'menu:sync-aliases
                            {--csv= : Local CSV instead of fetching the sheet}
                            {--apply : Write changes (default is a dry run)}';

    protected $description = 'Sync prices for foods whose app name differs from the menu sheet name';

    private const SHEET_URL = 'https://docs.google.com/spreadsheets/d/1Sv5x2MLn9ptOlFGK8fCu4r8DurQdPJYVWGq4g_mJpmM/export?format=csv';

    /** food id => exact sheet item name. Verified one by one against the sheet. */
    private const ALIASES = [
        // portion suffix in the app, absent from the sheet
        138 => 'كبة مقلية',
        141 => 'سمبوسك جبنة',
        142 => 'سمبوسك لحمة',
        146 => 'حمص بيروتي',
        147 => 'بابا غنوج',
        149 => 'ثومية',
        150 => 'ثومية اسبايسي',
        // redundant prefix in the app
        153 => 'خيار بالبن',
        154 => 'شمندر',
        158 => 'روكا',
        169 => 'شوربة كريمة فراخ',
        170 => 'شوربة كريمة فراخ ومشروم',
        // spelling differences
        113 => 'بينا أربياتا',
        115 => 'مكرونة فوتوشينى',
        116 => 'بينا الفريدو',
        117 => 'اسباجيتي بولونيز',
        127 => 'حنيد لحم',
        129 => 'حنيد فراخ',
        130 => 'زربيان لحم',
        128 => 'نص فرخة مندي',
        136 => 'عيش فرن',
        163 => 'فتة شاورما فراخ',
        // صفايح: app sells by the piece, sheet names it explicitly
        191 => 'صفيحة لحم طماطم ( قطعة )',
        192 => 'صفيحة لحم زبادى ( قطعة )',
    ];

    private function normalize(string $s): string
    {
        $s = trim($s);
        $s = preg_replace('/[\x{064B}-\x{0652}\x{0640}]/u', '', $s);
        $s = preg_replace('/[\x{0622}\x{0623}\x{0625}\x{0671}]/u', "\u{0627}", $s);
        $s = str_replace(["\u{0629}", "\u{0649}", "\u{0624}", "\u{0626}"],
                         ["\u{0647}", "\u{064A}", "\u{0648}", "\u{064A}"], $s);
        return trim(preg_replace('/\s+/u', ' ', $s));
    }

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');

        $path = $this->option('csv');
        $raw = $path ? @file_get_contents($path) : @file_get_contents(self::SHEET_URL);
        if ($raw === false || trim($raw) === '') {
            $this->error('Could not read menu CSV.');
            return self::FAILURE;
        }
        $raw = preg_replace('/^\x{FEFF}/u', '', $raw);
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        array_shift($lines);

        $sheet = [];
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
            $sheet[$this->normalize($name)][] = (float) $price;
        }

        $changes = $same = $problems = [];

        foreach (self::ALIASES as $foodId => $sheetName) {
            $food = DB::table('food')->where('id', $foodId)->first();
            if (!$food) {
                $problems[] = "#{$foodId} not found in DB";
                continue;
            }

            $key = $this->normalize($sheetName);
            if (!isset($sheet[$key])) {
                $problems[] = "#{$foodId} {$food->name}: sheet row '{$sheetName}' not found";
                continue;
            }

            $prices = array_unique($sheet[$key]);
            if (count($prices) > 1) {
                $problems[] = "#{$foodId} {$food->name}: sheet has conflicting prices ".implode('/', $prices);
                continue;
            }

            $new = (float) reset($prices);
            $old = (float) $food->price;

            if (abs($new - $old) < 0.01) {
                $same[] = [$foodId, $food->name];
                continue;
            }
            $changes[] = ['id' => $foodId, 'name' => $food->name, 'sheet' => $sheetName, 'old' => $old, 'new' => $new];
        }

        $this->line('<comment>Price changes: '.count($changes).'</comment>');
        foreach ($changes as $c) {
            $arrow = $c['new'] > $c['old'] ? 'up' : 'down';
            $this->line(sprintf('  #%-5d %-30s %7.2f -> %7.2f  %-5s (sheet: %s)',
                $c['id'], mb_strimwidth($c['name'], 0, 30, '…'), $c['old'], $c['new'], $arrow, $c['sheet']));
        }

        if ($problems) {
            $this->newLine();
            $this->line('<comment>Problems (skipped): '.count($problems).'</comment>');
            foreach ($problems as $p) {
                $this->line('  '.$p);
            }
        }

        $this->newLine();
        $this->info(sprintf('Summary: %d to change, %d already correct, %d problems.',
            count($changes), count($same), count($problems)));

        if (!$apply) {
            $this->newLine();
            $this->warn('DRY RUN - nothing written. Re-run with --apply to commit.');
            return self::SUCCESS;
        }

        if (!$changes) {
            $this->info('Nothing to write.');
            return self::SUCCESS;
        }

        DB::beginTransaction();
        try {
            foreach ($changes as $c) {
                DB::table('food')->where('id', $c['id'])->update(['price' => $c['new'], 'updated_at' => now()]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Rolled back, nothing written: '.$e->getMessage());
            return self::FAILURE;
        }

        $this->info('Applied '.count($changes).' price updates.');
        return self::SUCCESS;
    }
}
