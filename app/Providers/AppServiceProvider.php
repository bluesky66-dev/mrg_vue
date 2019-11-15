<?php

namespace Momentum\Providers;

use Illuminate\Support\ServiceProvider;

use Momentum\Enums\Agreements;
use Momentum\Enums\Emphasis;
use Momentum\Enums\Frequencies;
use Momentum\Enums\ObserverTypes;
use Momentum\Enums\ReportStatuses;
use Validator;

/**
 * Application service provder.
 * Developed by laravel.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('emphasis', function ($attribute, $value, $parameters, $validator) {
            return in_array($value, Emphasis::options());
        });

        Validator::extend('frequency', function ($attribute, $value, $parameters, $validator) {
            return in_array($value, Frequencies::options());
        });

        Validator::extend('observer_type', function ($attribute, $value, $parameters, $validator) {
            return in_array($value, ObserverTypes::options());
        });

        Validator::extend('report_status', function ($attribute, $value, $parameters, $validator) {
            return in_array($value, ReportStatuses::options());
        });

        Validator::extend('agreements', function ($attribute, $value, $parameters, $validator) {
            return in_array($value, Agreements::options());
        });

        Validator::extend('textarea', function ($attribute, $value, $parameters, $validator) {
            if (strlen($value) > config('momentum.max_char_length')) {
                return false;
            }

            return true;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
