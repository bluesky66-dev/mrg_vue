<?php

namespace Momentum\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

/**
 * Broadcast service provder.
 * Developed by laravel.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        require base_path('routes/channels.php');
    }
}
