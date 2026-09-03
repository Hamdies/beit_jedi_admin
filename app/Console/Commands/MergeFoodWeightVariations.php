<?php

namespace App\Console\Commands;

use App\Models\Food;
use App\Models\Variation;
use App\Models\VariationOption;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Merges sibling products that differ only by weight into a single product
 * carrying a required single-select "الوزن" variation.
 *
 * Example: "1/4 ك شاورما فراخ" (260), "1/2 ك شاورما فراخ" (500) and
 * "كيلو شاورما فراخ" (1000) become one "شاورما فراخ" priced at 260 with
 * options 1/4 ك (+0), 1/2 ك (+240), كيلو (+740).
 *
 * Option prices are ADDITIVE on top of the base price — see
 * item_details_screen.dart _getVariationPrice() in the Flutter app — so the
 * base is the cheapest weight and each option carries the difference.
 *
 * The absorbed products are ARCHIVED (status = 0), never deleted, so existing
 * carts, order_details and reorder history keep resolving.
 */
class MergeFoodWeightVariations extends Command
{
    protected $signature = 'food:merge-weights
                            {--dry-run : Report what would change without writing anything}
                            {--restaurant= : Limit to a single restaurant id}
                            {--variation-name=الوزن : Name of the variation group to create}';

    protected $description = 'Merge weight-sibling food products into one product with a weight variation';

    /**
     * Weight prefixes we recognise, longest first so "1/4 ك" wins over "ك".
     * Each entry maps a set of spellings to the canonical option label.
     */
    private const WEIGHT_PATTERNS = [
        'ربع كيلو'  => '1/4 ك',
        '1/4 كيلو'  => '1/4 ك',
        '١/٤ ك'     => '1/4 ك',
        '1/4 ك'     => '1/4 ك',
        'نص كيلو'   => '1/2 ك',
        'نصف كيلو'  => '1/2 ك',
        '1/2 كيلو'  => '1/2 ك',
        '١/٢ ك'     => '1/2 ك',
        '1/2 ك'     => '1/2 ك',
        'كيلو'      => 'كيلو',
    ];

    /** Sort order for the option list, lightest first. */
    private const WEIGHT_ORDER = ['1/4 ك' => 1, '1/2 ك' => 2, 'كيلو' => 3];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN — no changes will be written.');
        } else {
            $this->warn('LIVE RUN — this will modify the database.');
            if (! $this->confirm('Have you taken a database backup?', false)) {
                $this->error('Aborted. Take a backup first.');
                return self::FAILURE;
            }
        }
        $this->newLine();

        $groups = $this->discoverGroups();

        if (empty($groups)) {
            $this->warn('No weight-sibling groups found. Nothing to do.');
            return self::SUCCESS;
        }

        $this->info(sprintf('Found %d mergeable group(s).', count($groups)));
        $this->newLine();

        $blocked = 0;
        $merged  = 0;

        foreach ($groups as $baseName => $items) {
            if (! $this->reportGroup($baseName, $items)) {
                $blocked++;
                continue;
            }

            if (! $dryRun) {
                DB::transaction(fn () => $this->mergeGroup($baseName, $items));
                $this->line('  <info>merged.</info>');
                $merged++;
            }
            $this->newLine();
        }

        if ($blocked > 0) {
            $this->warn(sprintf('%d group(s) skipped — see reasons above.', $blocked));
        }

        if ($dryRun) {
            $this->info('Dry run complete. Re-run without --dry-run to apply.');
        } else {
            $this->info(sprintf('Done. %d group(s) merged.', $merged));
            $this->line('Absorbed products were archived (status = 0), not deleted.');
        }

        return self::SUCCESS;
    }

    /**
     * Group active products by the name left over once a weight prefix is removed.
     * Only groups with 2+ members are mergeable.
     *
     * Global scopes are dropped: ZoneScope is registered unconditionally on Food
     * and would hide rows from a CLI run with no request context.
     */
    private function discoverGroups(): array
    {
        $query = Food::withoutGlobalScopes()->where('status', 1);

        if ($restaurantId = $this->option('restaurant')) {
            $query->where('restaurant_id', $restaurantId);
        }

        $groups = [];

        foreach ($query->get() as $food) {
            [$weight, $baseName] = $this->splitWeight((string) $food->name);

            if ($weight === null || $baseName === '') {
                continue;
            }

            // Keep restaurant and category in the key so unrelated products that
            // happen to share a name are never merged together.
            $key = $baseName;
            $groups[$key][] = [
                'food'   => $food,
                'weight' => $weight,
            ];
        }

        return array_filter($groups, fn ($items) => count($items) >= 2);
    }

    /**
     * Split "1/4 ك شاورما فراخ" into ['1/4 ك', 'شاورما فراخ'].
     * Returns [null, original] when no weight prefix is present.
     */
    private function splitWeight(string $name): array
    {
        $normalised = trim(preg_replace('/\s+/u', ' ', $name));

        foreach (self::WEIGHT_PATTERNS as $needle => $label) {
            // Only treat it as a weight when it leads the name, so a dish merely
            // mentioning a weight mid-sentence is left alone.
            if (mb_strpos($normalised, $needle) === 0) {
                $rest = trim(mb_substr($normalised, mb_strlen($needle)));
                return [$label, $rest];
            }
        }

        return [null, $normalised];
    }

    /**
     * Print the plan for one group and return whether it is safe to merge.
     */
    private function reportGroup(string $baseName, array $items): bool
    {
        usort($items, fn ($a, $b) => ($this->price($a['food'])) <=> ($this->price($b['food'])));

        $survivor = $items[0]['food'];
        $basePrice = $this->price($survivor);

        $this->line("<comment>{$baseName}</comment>");
        $this->line(sprintf(
            '  survivor: #%d "%s" — base price %s',
            $survivor->id,
            $survivor->name,
            number_format($basePrice, 2)
        ));

        $rows = [];
        foreach ($items as $item) {
            $food = $item['food'];
            $rows[] = [
                $item['weight'],
                '#' . $food->id,
                number_format($this->price($food), 2),
                '+' . number_format($this->price($food) - $basePrice, 2),
                $food->id === $survivor->id ? 'keep' : 'archive',
            ];
        }

        usort($rows, fn ($a, $b) => (self::WEIGHT_ORDER[$a[0]] ?? 99) <=> (self::WEIGHT_ORDER[$b[0]] ?? 99));
        $this->table(['weight', 'food', 'price', 'option price', 'action'], $rows);

        $safe = true;

        // The Flutter app reads new-style variations only when the legacy
        // `variations` text column is null/empty — see Food::getVariationsAttribute.
        $legacy = $survivor->getRawOriginal('variations');
        if (is_string($legacy) && $legacy !== '' && $legacy !== '[]' && json_decode($legacy, true)) {
            $this->line('  <comment>note:</comment> survivor has legacy variations JSON; it will be cleared.');
        }

        if ($survivor->newVariations()->count() > 0) {
            $this->error('  BLOCKED: survivor already has variations. Resolve by hand.');
            $safe = false;
        }

        // Archiving keeps these rows resolvable, but the operator should still know.
        $absorbedIds = collect($items)
            ->pluck('food.id')
            ->reject(fn ($id) => $id === $survivor->id)
            ->values()
            ->all();

        $cartCount = DB::table('carts')->whereIn('item_id', $absorbedIds)->count();
        if ($cartCount > 0) {
            $this->line("  <comment>note:</comment> {$cartCount} live cart row(s) reference absorbed products (kept working by archiving).");
        }

        $orderCount = DB::table('order_details')->whereIn('food_id', $absorbedIds)->count();
        if ($orderCount > 0) {
            $this->line("  <comment>note:</comment> {$orderCount} historical order row(s) reference absorbed products.");
        }

        return $safe;
    }

    /**
     * Create the variation + options on the survivor and archive the siblings.
     */
    private function mergeGroup(string $baseName, array $items): void
    {
        usort($items, fn ($a, $b) => ($this->price($a['food'])) <=> ($this->price($b['food'])));

        $survivor  = $items[0]['food'];
        $basePrice = $this->price($survivor);

        $variation = Variation::create([
            'food_id'     => $survivor->id,
            'name'        => (string) $this->option('variation-name'),
            'type'        => 'single',
            'min'         => 0,
            'max'         => 0,
            'is_required' => true,
        ]);

        $ordered = $items;
        usort(
            $ordered,
            fn ($a, $b) => (self::WEIGHT_ORDER[$a['weight']] ?? 99) <=> (self::WEIGHT_ORDER[$b['weight']] ?? 99)
        );

        foreach ($ordered as $item) {
            VariationOption::create([
                'food_id'      => $survivor->id,
                'variation_id' => $variation->id,
                'option_name'  => $item['weight'],
                'option_price' => $this->price($item['food']) - $basePrice,
                'total_stock'  => 0,
                'stock_type'   => 'unlimited',
                'sell_count'   => 0,
            ]);
        }

        // Rename to the weightless base name and clear the legacy column so the
        // API serves the new-style variations.
        Food::withoutGlobalScopes()->where('id', $survivor->id)->update([
            'name'       => $baseName,
            'price'      => $basePrice,
            'variations' => null,
        ]);

        foreach ($items as $item) {
            if ($item['food']->id === $survivor->id) {
                continue;
            }
            Food::withoutGlobalScopes()->where('id', $item['food']->id)->update(['status' => 0]);
        }
    }

    private function price(Food $food): float
    {
        return (float) $food->getRawOriginal('price');
    }
}
