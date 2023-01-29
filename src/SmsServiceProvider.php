<?php

namespace Antto\Sms;

use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/sms.php',
            'sms'
        );

        $this->app->singleton('sms', function ($app) {
            return new Sms();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/sms.php' => config_path('sms.php'),
        ], 'sms-config');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'sms-migrations');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'sms');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/sms'),
        ], 'sms-lang');
    }
}
