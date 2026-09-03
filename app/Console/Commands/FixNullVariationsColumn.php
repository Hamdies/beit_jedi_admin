<?php

namespace App\Console\Commands;

use App\Models\Food;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Repairs food rows whose legacy `variations` column is NULL.
 *
 * Food::getVariationsAttribute() gates its new-style variation building on
 * is_string($value). A NULL column fails that check, so the accessor never
 * assembles JSON from the variations/variation_options tables and the API
 * serves no variations — the product looks like it has none.
 *
 * The column must hold the string '[]' (what the admin panel writes via
 * json_encode([])) for the accessor to take over.
 */
class FixNullVariationsColumn extends Command
{
    protected $signature = 'food:fix-null-variations {--dry-run : Report without writing}';

    protected $description = 'Set the legacy variations column to \'[]\' where it is NULL so new-style variations serialize';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        // Raw query: the Eloquent accessor rewrites the value we need to inspect.
        $rows = DB::table('food')
            ->whereNull('variations')
            ->select('id', 'name')
            ->get();

        if ($rows->isEmpty()) {
            $this->info('No rows with a NULL variations column. Nothing to do.');
            return self::SUCCESS;
        }

        $this->info(sprintf('%d row(s) have a NULL variations column:', $rows->count()));

        $table = [];
        foreach ($rows as $row) {
            $optionCount = DB::table('variation_options')->where('food_id', $row->id)->count();
            $table[] = [
                '#' . $row->id,
                $row->name,
                $optionCount > 0 ? "{$optionCount} option(s) waiting" : 'no options',
            ];
        }
        $this->table(['food', 'name', 'new-style variations'], $table);

        if ($dryRun) {
            $this->info('Dry run — re-run without --dry-run to apply.');
            return self::SUCCESS;
        }

        $updated = DB::table('food')
            ->whereNull('variations')
            ->update(['variations' => json_encode([])]);

        $this->info("Updated {$updated} row(s) to '[]'.");
        $this->line('Products with new-style variations will now serialize them through the API.');

        return self::SUCCESS;
    }
}
