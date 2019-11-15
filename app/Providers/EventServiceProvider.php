<?php

namespace Momentum\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Event service provder.
 * Developed by laravel.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Notifications\Events\NotificationSent' => [
            'Momentum\Listeners\LogNotification',
        ],
        'Momentum\Events\Organization\OrganizationCreated' => [
            'Momentum\Listeners\CreateDefaultOrganizationGoals',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
