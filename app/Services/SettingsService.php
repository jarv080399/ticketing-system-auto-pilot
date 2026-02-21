<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    protected const CACHE_KEY = 'system_settings_cache';

    /**
     * Get all settings grouped by group name.
     */
    public function getAllGrouped(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return SystemSetting::all()->groupBy('group')->toArray();
        });
    }

    /**
     * Get a single setting value by key.
     */
    public function get(string $key, $default = null)
    {
        $settings = $this->getAllFlattened();
        
        return $settings[$key] ?? $default;
    }

    /**
     * Update multiple settings.
     */
    public function updateBatch(array $settings, int $userId): void
    {
        foreach ($settings as $key => $value) {
            SystemSetting::where('key', $key)->update([
                'value' => is_array($value) ? json_encode($value) : $value,
                'updated_by' => $userId,
            ]);
        }

        $this->clearCache();
    }

    /**
     * Clear the settings cache.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_KEY . '_flat');
    }

    /**
     * Get all settings flattened (key => value).
     */
    protected function getAllFlattened(): array
    {
        return Cache::rememberForever(self::CACHE_KEY . '_flat', function () {
            return SystemSetting::all()->pluck('value', 'key')->toArray();
        });
    }
}
