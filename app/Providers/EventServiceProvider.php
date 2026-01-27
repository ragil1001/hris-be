<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\KaryawanCreated;
use App\Events\KaryawanUpdated;
use App\Events\KaryawanDeleted;
use App\Listeners\LogKaryawanActivity;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserLoggedIn::class => [
            LogUserLoginLogout::class,
        ],
        UserLoggedOut::class => [
            LogUserLoginLogout::class,
        ],
        KaryawanCreated::class => [
            LogKaryawanActivity::class,
        ],
        KaryawanUpdated::class => [
            LogKaryawanActivity::class,
        ],
        KaryawanDeleted::class => [
            LogKaryawanActivity::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
