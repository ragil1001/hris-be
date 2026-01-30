<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\LogAuthentication::class,
        ],
        \Illuminate\Auth\Events\Logout::class => [
            \App\Listeners\LogAuthentication::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
