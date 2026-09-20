<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Removes products that are now duplicated by a variation group.
 *
 * #331/#332/#333 sell بوكس شاورما types as standalone products, but #160
 * now carries all six type/اكسترا combinations as options, so customers
 * see بوكس twice. Same pattern the weight merge left behind for شاورما.
 *
 * Also resolves two soups duplicated as separate rows (#166/#210 and
 * #168/#211), keeping the row with the sheet price and archiving the other.
 *
 * Everything is archived (status = 0), never deleted, so carts and order
 * history keep resolving.
 */
class CleanupMenuDuplicates extends Command
{
    protected $signature = 'menu:cleanup-duplicates
                            {--apply : Write changes (default is a dry run)}';

    protected $description = 'Archive بوكس products duplicated by #160 variations and resolve duplicate soup rows';

    /** id => [reason, survivor id that covers it] */
    private const ARCHIVE = [
        331 => ['duplicated by #160 option لحمة (270)', 160],
        332 => ['duplicated by #160 option ميكس (250)', 160],
        333 => ['duplicated by #160 option فراخ اكسترا (250)', 160],
        166 => ['duplicate of #210 شوربة عدس', 210],
        168 => ['duplicate of #211 شوربة لسان عصفور', 211],
    ];

    /** Sheet prices for the surviving soup rows. */
    private const SOUP_PRICES = [210 => 70.0, 211 => 65.0];

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');

        $ids = array_merge(array_keys(self::ARCHIVE), array_values(array_unique(array_column(self::ARCHIVE, 1))));
        $rows = DB::table('food')->whereIn('id', $ids)->get()->keyBy('id');

        // Never archive a row unless the survivor that replaces it is live.
        foreach (self::ARCHIVE as $id => [$why, $survivor]) {
            $s = $rows->get($survivor);
            if (!$s || (int) $s->status !== 1) {
                $this->error("Survivor #{$survivor} missing or inactive - aborting, nothing archived.");
                return self::FAILURE;
            }
        }

        $todo = [];
        foreach (self::ARCHIVE as $id => [$why, $survivor]) {
            $r = $rows->get($id);
            if (!$r) {
                $this->warn("  #{$id} not found - skipping.");
                continue;
            }
            if ((int) $r->status === 0) {
                $this->line("  #{$id} {$r->name} - already archived.");
                continue;
            }
            $todo[$id] = [$r, $why];
        }

        $this->line('<comment>To archive: '.count($todo).'</comment>');
        foreach ($todo as $id => [$r, $why]) {
            $this->line(sprintf('  #%-5d %-32s %7.2f  orders %-3d  %s',
                $id, mb_strimwidth($r->name, 0, 32, '…'), $r->price, $r->order_count, $why));
        }

        $soupFixes = [];
        foreach (self::SOUP_PRICES as $id => $want) {
            $r = $rows->get($id);
            if ($r && abs((float) $r->price - $want) > 0.01) {
                $soupFixes[$id] = ['name' => $r->name, 'old' => (float) $r->price, 'new' => $want];
            }
        }
        if ($soupFixes) {
            $this->newLine();
            $this->line('<comment>Surviving soup prices to correct: '.count($soupFixes).'</comment>');
            foreach ($soupFixes as $id => $f) {
                $this->line(sprintf('  #%-5d %-32s %7.2f -> %7.2f', $id, $f['name'], $f['old'], $f['new']));
            }
        }

        if (!$todo && !$soupFixes) {
            $this->newLine();
            $this->info('Nothing to do.');
            return self::SUCCESS;
        }

        if (!$apply) {
            $this->newLine();
            $this->warn('DRY RUN - nothing written. Re-run with --apply to commit.');
            return self::SUCCESS;
        }

        DB::beginTransaction();
        try {
            if ($todo) {
                DB::table('food')->whereIn('id', array_keys($todo))->update(['status' => 0, 'updated_at' => now()]);
            }
            foreach ($soupFixes as $id => $f) {
                DB::table('food')->where('id', $id)->update(['price' => $f['new'], 'updated_at' => now()]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Rolled back, nothing written: '.$e->getMessage());
            return self::FAILURE;
        }

        $this->info('Archived '.count($todo).' products, corrected '.count($soupFixes).' prices.');
        return self::SUCCESS;
    }
}
