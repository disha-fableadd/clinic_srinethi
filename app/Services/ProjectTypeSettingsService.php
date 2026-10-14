<?php

namespace App\Services;

use App\Models\ProjectType;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class ProjectTypeSettingsService
{
    private const DEFAULT_PROJECT_TYPE_ID = 1;
    private const SETTING_KEY = 'project_type_id';

    public function ensureDefaultProjectType(): void
    {
        $setting = Setting::firstOrCreate([
            'key' => self::SETTING_KEY,
        ], [
            'value' => self::DEFAULT_PROJECT_TYPE_ID,
        ]);

        if (blank($setting->value)) {
            $setting->update([
                'value' => self::DEFAULT_PROJECT_TYPE_ID,
            ]);
        }

        if (! ProjectType::query()->whereKey((int) $setting->value)->exists()) {
            Log::warning('Project type setting points to a missing project type record.', [
                'setting_key' => self::SETTING_KEY,
                'project_type_id' => $setting->value,
            ]);
        }
    }
}
