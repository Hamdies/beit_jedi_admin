<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Two variation groups menu:sync-variations cannot resolve on its own.
 *
 * #160 بوكس شاورما عربي: the sheet sells each type in a regular and an
 * اكسترا line (فراخ 225/250, مكس 250/280, لحمة 270/300) but the product
 * only carries the type picker.
 *
 * A second اكسترا group cannot work here: pricing is strictly additive
 * (Helpers::get_varient sums optionPrice), and the اكسترا surcharge is not
 * flat - +25 on فراخ but +30 on مكس and لحمة. Two independent groups can
 * only express one surcharge for all types, underpricing two of them.
 *
 * So the existing النوع group is widened to all six combinations, each
 * carrying its own absolute delta off base 225.
 *
 * #152 سندوتش شاورما فراخ: stored as base 0.10 with absolute option prices,
 * billing 100.10 / 110.10 / 120.10 / 150.10. Rebased to 100 with real
 * deltas, which also corrects فينو from 150 to the sheet's 140.
 */
class FixShawarmaBoxAndSandwich extends Command
{
    protected $signature = 'food:fix-box-and-sandwich
                            {--apply : Write changes (default is a dry run)}';

    protected $description = 'Fix بوكس شاورما عربي (add اكسترا size group) and سندوتش شاورما فراخ (rebase from 0.10)';

    private const BOX_FOOD = 160;
    private const SANDWICH_FOOD = 152;

    /**
     * Target absolute prices from the menu sheet, all six combinations.
     * Keys marked with => are existing option labels that get rebased;
     * the rest are added to the same group.
     */
    private const BOX_TYPES = ['فراخ' => 225.0, 'ميكس' => 250.0, 'لحمة' => 270.0];
    private const BOX_EXTRA = ['فراخ اكسترا' => 250.0, 'ميكس اكسترا' => 280.0, 'لحمة اكسترا' => 300.0];
    private const SANDWICH = ['خبز سياحي' => 100.0, 'خبز كيزر' => 110.0, 'خبز صاج' => 120.0, 'خبز فينو' => 140.0];

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');

        $box = DB::table('food')->where('id', self::BOX_FOOD)->first();
        $sandwich = DB::table('food')->where('id', self::SANDWICH_FOOD)->first();

        if (!$box || !$sandwich) {
            $this->error('Expected foods #160 and #152 not found. Aborting.');
            return self::FAILURE;
        }

        // ---- #160 بوكس ----
        $this->line('<comment>#160 '.$box->name.'</comment>');
        $boxVar = DB::table('variations')->where('food_id', self::BOX_FOOD)->where('name', 'النوع')->first();
        if (!$boxVar) {
            $this->error('  type variation النوع not found on #160. Aborting.');
            return self::FAILURE;
        }

        $boxOpts = DB::table('variation_options')->where('variation_id', $boxVar->id)->get();
        $newBase = min(self::BOX_TYPES);
        $typePlan = [];
        foreach ($boxOpts as $o) {
            $label = trim($o->option_name);
            $abs = self::BOX_TYPES[$label] ?? self::BOX_EXTRA[$label] ?? null;
            if ($abs === null) {
                $this->error('  unexpected option "'.$label.'" on #160 - aborting rather than guessing.');
                return self::FAILURE;
            }
            $typePlan[$o->id] = [
                'label' => $label,
                'old' => (float) $o->option_price,
                'new' => $abs - $newBase,
            ];
        }
        if (count($typePlan) < count(self::BOX_TYPES)) {
            $this->error('  expected at least '.count(self::BOX_TYPES).' type options, found '.count($typePlan).'. Aborting.');
            return self::FAILURE;
        }

        // Any اكسترا option already present is rebased, not duplicated.
        $present = $boxOpts->pluck('option_name')->map(fn ($n) => trim($n))->all();
        $extraPlan = [];
        foreach (self::BOX_EXTRA as $label => $abs) {
            if (!in_array($label, $present, true)) {
                $extraPlan[$label] = $abs;
            }
        }

        $this->line(sprintf('  base %.2f -> %.2f', $box->price, $newBase));
        foreach ($typePlan as $t) {
            $this->line(sprintf('    %-14s delta %7.2f -> %7.2f   customer pays %7.2f -> %7.2f',
                $t['label'], $t['old'], $t['new'],
                (float) $box->price + $t['old'], $newBase + $t['new']));
        }
        foreach ($extraPlan as $label => $abs) {
            $this->line(sprintf('    %-14s NEW option           delta +%7.2f   customer pays %7.2f',
                $label, $abs - $newBase, $abs));
        }

        // ---- #152 سندوتش ----
        $this->newLine();
        $this->line('<comment>#152 '.$sandwich->name.'</comment>');
        $sVar = DB::table('variations')->where('food_id', self::SANDWICH_FOOD)->first();
        if (!$sVar) {
            $this->error('  no variation group found on #152. Aborting.');
            return self::FAILURE;
        }
        $sOpts = DB::table('variation_options')->where('variation_id', $sVar->id)->get();
        $sBase = min(self::SANDWICH);
        $sPlan = [];
        foreach ($sOpts as $o) {
            $label = trim($o->option_name);
            if (!array_key_exists($label, self::SANDWICH)) {
                $this->error('  unexpected bread option "'.$label.'" - aborting.');
                return self::FAILURE;
            }
            $sPlan[$o->id] = [
                'label' => $label,
                'old' => (float) $o->option_price,
                'new' => self::SANDWICH[$label] - $sBase,
                'before' => (float) $sandwich->price + (float) $o->option_price,
                'after' => self::SANDWICH[$label],
            ];
        }

        $this->line(sprintf('  base %.2f -> %.2f', $sandwich->price, $sBase));
        foreach ($sPlan as $t) {
            $this->line(sprintf('    %-12s delta %7.2f -> %7.2f   customer pays %7.2f -> %7.2f',
                $t['label'], $t['old'], $t['new'], $t['before'], $t['after']));
        }

        $allDeltas = array_merge(
            array_column($typePlan, 'new'),
            array_column($sPlan, 'new'),
            array_map(fn ($a) => $a - $newBase, array_values($extraPlan))
        );
        foreach ($allDeltas as $d) {
            if ($d < 0) {
                $this->error('Refusing to write: negative delta computed.');
                return self::FAILURE;
            }
        }

        if (!$apply) {
            $this->newLine();
            $this->warn('DRY RUN - nothing written. Re-run with --apply to commit.');
            return self::SUCCESS;
        }

        DB::beginTransaction();
        try {
            DB::table('food')->where('id', self::BOX_FOOD)->update(['price' => $newBase, 'updated_at' => now()]);
            foreach ($typePlan as $id => $t) {
                DB::table('variation_options')->where('id', $id)->update(['option_price' => $t['new'], 'updated_at' => now()]);
            }

            foreach ($extraPlan as $label => $abs) {
                DB::table('variation_options')->insert([
                    'food_id' => self::BOX_FOOD,
                    'variation_id' => $boxVar->id,
                    'option_name' => $label,
                    'option_price' => $abs - $newBase,
                    'total_stock' => 0,
                    'stock_type' => 'unlimited',
                    'sell_count' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('food')->where('id', self::SANDWICH_FOOD)->update(['price' => $sBase, 'updated_at' => now()]);
            foreach ($sPlan as $id => $t) {
                DB::table('variation_options')->where('id', $id)->update(['option_price' => $t['new'], 'updated_at' => now()]);
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
