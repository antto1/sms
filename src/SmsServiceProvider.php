<?php

namespace Antto\Sms;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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

        Validator::extend('is_mobile', 'Antto\Sms\Validator@isMobile');
        Validator::extend('can_send', 'Antto\Sms\Validator@canSend');
        Validator::extend('verify_code', 'Antto\Sms\Validator@verifyCode');
    }
}
