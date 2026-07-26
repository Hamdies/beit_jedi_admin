<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Seed the Akedly OTP gateway row so it appears in the admin panel
     * (Business Settings → SMS Module) for configuration.
     */
    public function up(): void
    {
        if (!Schema::hasTable('addon_settings')) {
            return;
        }

        $exists = DB::table('addon_settings')
            ->where('key_name', 'akedly')
            ->where('settings_type', 'sms_config')
            ->exists();

        if ($exists) {
            return;
        }

        $values = json_encode([
            'gateway' => 'akedly',
            'mode' => 'test',
            'status' => 0,
            'api_key' => '',
            'pipeline_id' => '',
        ]);

        DB::table('addon_settings')->insert([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'key_name' => 'akedly',
            'live_values' => $values,
            'test_values' => $values,
            'settings_type' => 'sms_config',
            'mode' => 'test',
            'is_active' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (Schema::hasTable('addon_settings')) {
            DB::table('addon_settings')
                ->where('key_name', 'akedly')
                ->where('settings_type', 'sms_config')
                ->delete();
        }
    }
};
