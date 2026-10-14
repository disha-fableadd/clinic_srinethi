<?php

namespace App\Providers;

use App\Models\Branch;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            try {
                $branches = Branch::all();
            } catch (\Throwable $e) {
                $branches = collect(); // avoid crash if table missing
            }

            $view->with('allBranches', $branches);
        });

        $settings = [];
        try {
            if (Schema::hasTable('settings')) {
                $settings = Setting::pluck('value', 'key')->toArray();
            }
        } catch (\Throwable $e) {
            $settings = [];
        }

        $mailConfigMap = [
            'mail_host' => 'mail.mailers.smtp.host',
            'mail_port' => 'mail.mailers.smtp.port',
            'mail_username' => 'mail.mailers.smtp.username',
            'mail_password' => 'mail.mailers.smtp.password',
            'mail_encryption' => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name' => 'mail.from.name',
        ];

        foreach ($mailConfigMap as $settingKey => $configKey) {
            if (array_key_exists($settingKey, $settings) && $settings[$settingKey] !== null && $settings[$settingKey] !== '') {
                Config::set($configKey, $settings[$settingKey]);
            }
        }

        $this->app->singleton('hasPermission', function () {
            return function ($moduleId, $action) {
                $user = Auth::user();
                if (!$user) {
                    return false;
                }

                // Allow full access if the user is an admin
                if ($user->role_id == '2') {
                    return true;
                }

                if (!isset($user->permissions)) {
                    return false;
                }

                $permission = collect($user->permissions)->firstWhere('module_id', $moduleId);
                return $permission ? ($permission[$action] == 1) : false;
            };
        });

    }
}
