<?php

namespace App\Observers;

use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BusinessSettingObserver
{
    /**
     * Handle the BusinessSetting "created" event.
     */
    public function created(BusinessSetting $businessSetting): void
    {
        $this->refreshBusinessSettingsCache();
    }

    /**
     * Handle the BusinessSetting "updated" event.
     */
    public function updated(BusinessSetting $businessSetting): void
    {
        $this->refreshBusinessSettingsCache();
    }

    /**
     * Handle the BusinessSetting "deleted" event.
     */
    public function deleted(BusinessSetting $businessSetting): void
    {
        $this->refreshBusinessSettingsCache();
    }

    /**
     * Handle the BusinessSetting "restored" event.
     */
    public function restored(BusinessSetting $businessSetting): void
    {
        $this->refreshBusinessSettingsCache();
    }

    /**
     * Handle the BusinessSetting "force deleted" event.
     */
    public function forceDeleted(BusinessSetting $businessSetting): void
    {
        $this->refreshBusinessSettingsCache();
    }

    private function refreshBusinessSettingsCache()
    {
        // Forget the known keys directly: the prefix-stripping sweep below only
        // works when APP_NAME happens to match Laravel's slugged cache prefix.
        foreach (['business_settings_all_data', 'business_settings_keys', 'business_settings_logo_storage', 'business_settings_icon_storage'] as $key) {
            Cache::forget($key);
        }

        if (config('cache.default') !== 'database') {
            return;
        }

        $prefix = 'business_settings_';
        $cacheKeys = DB::table('cache')
            ->where('key', 'like', "%" . $prefix . "%")
            ->pluck('key');
        $remove_prefix = config('cache.prefix');
        $sanitizedKeys = $cacheKeys->map(function ($key) use ($remove_prefix) {
            return str_starts_with($key, $remove_prefix) ? substr($key, strlen($remove_prefix)) : $key;
        });
        foreach ($sanitizedKeys as $key) {
            Cache::forget($key);
        }
    }
}
