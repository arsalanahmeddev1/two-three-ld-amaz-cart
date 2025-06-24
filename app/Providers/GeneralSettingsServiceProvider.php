<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Modules\GeneralSetting\Entities\BusinessSetting;
use Modules\GeneralSetting\Entities\GeneralSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class GeneralSettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (Schema::hasTable('general_settings')) {
            app()->singleton('general_setting', function () {
                return GeneralSetting::first();
            });
            $config = GeneralSetting::first();
            if($config){
                config(['app.name' => $config->site_title]);
                config(['app.url' => $config->site_url]);
                config(['app.asset_url' => $config->site_url]);
                config(['app.force_https' => $config->force_ssl]);
                config(['app.timezone' => $config->time_zone]);

                if (Schema::hasTable('business_settings')) {
                    $currency_config = BusinessSetting::where('type','currency')->first();
                    if($currency_config){
                        Config::set('currency', $currency_config->value);
                    }
                }
            }
        }
    }
}
