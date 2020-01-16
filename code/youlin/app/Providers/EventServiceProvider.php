<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Events\QueryExecuted;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class    => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\NewUser' => [
            'App\Listeners\RegisterUser',
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
        if (env('APP_DEBUG')) {
            DB::listen(function (QueryExecuted $query) {
                Log::info(sprintf(str_replace('?', "'%s'", $query->sql), ...$query->bindings));
            });
        }
    }
}
