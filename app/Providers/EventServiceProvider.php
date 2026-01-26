<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use App\Listeners\LogUserLoginLogout;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserLoggedIn::class => [
            LogUserLoginLogout::class,
        ],
        UserLoggedOut::class => [
            LogUserLoginLogout::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
