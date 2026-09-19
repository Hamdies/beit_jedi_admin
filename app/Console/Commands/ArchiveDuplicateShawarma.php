<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Finishes the interrupted food:merge-weights migration in category 11.
 *
 * Two generations of shawarma products were left live at once:
 *   - #201/#202/#203: the older "الحجم" set, stale prices, superseded
 *   - #336/#339/#342: the current "الوزن" set, matches the menu sheet
 * plus #337/#338, weight siblings of شاورما فراخ that were never archived
 * the way their لحمة/مكس equivalents were.
 *
 * Archives (status = 0) rather than deletes, so carts, order_details and
 * reorder history keep resolving - same contract as MergeFoodWeightVariations.
 */
class ArchiveDuplicateShawarma extends Command
{
    protected $signature = 'food:archive-duplicate-shawarma
                            {--apply : Write changes (default is a dry run)}';

    protected $description = 'Archive superseded duplicate shawarma products left behind by the weight merge';

    /** id => why it is being archived. Explicit list: no name guessing. */
    private const TARGETS = [
        201 => 'superseded by #336 (الوزن set)',
        202 => 'superseded by #339 (الوزن set)',
        203 => 'superseded by #342 (الوزن set)',
        337 => 'weight sibling absorbed into #336',
        338 => 'weight sibling absorbed into #338 parent (#336)',
    ];

    /** Must stay live; archiving one of these would remove a real product. */
    private const KEEP = [336, 339, 342];

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');

        $rows = DB::table('food')
            ->whereIn('id', array_merge(array_keys(self::TARGETS), self::KEEP))
            ->get(['id', 'name', 'price', 'status', 'order_count'])
            ->keyBy('id');

        // Refuse to archive anything if a survivor is missing or already inactive.
        foreach (self::KEEP as $id) {
            $row = $rows->get($id);
            if (!$row) {
                $this->error("Survivor #{$id} not found. Aborting - nothing archived.");
                return self::FAILURE;
            }
            if ((int) $row->status !== 1) {
                $this->error("Survivor #{$id} ({$row->name}) is not active (status={$row->status}). Aborting.");
                return self::FAILURE;
            }
        }
        $this->info('Survivors verified active: #'.implode(', #', self::KEEP));
        $this->newLine();

        $todo = [];
        foreach (self::TARGETS as $id => $why) {
            $row = $rows->get($id);
            if (!$row) {
                $this->warn("  #{$id} not found - skipping.");
                continue;
            }
            if ((int) $row->status === 0) {
                $this->line("  #{$id} {$row->name} - already archived, skipping.");
                continue;
            }
            $todo[$id] = [$row, $why];
        }

        $this->newLine();
        $this->line('<comment>To archive: '.count($todo).'</comment>');
        foreach ($todo as $id => [$row, $why]) {
            $this->line(sprintf('  #%-5d %-28s price %8.2f  orders %-4d  %s',
                $id, mb_strimwidth($row->name, 0, 28, '…'), $row->price, $row->order_count, $why));
        }

        if (!$todo) {
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
            DB::table('food')
                ->whereIn('id', array_keys($todo))
                ->update(['status' => 0, 'updated_at' => now()]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Rolled back, nothing written: '.$e->getMessage());
            return self::FAILURE;
        }

        $this->info('Archived '.count($todo).' products (status = 0, not deleted).');
        return self::SUCCESS;
    }
}
