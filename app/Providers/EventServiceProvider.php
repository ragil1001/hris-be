<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\KaryawanCreated;
use App\Events\KaryawanUpdated;
use App\Events\KaryawanDeleted;
use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use App\Events\ProjectCreated;
use App\Events\ProjectUpdated;
use App\Events\ProjectDeleted;
use App\Listeners\LogKaryawanActivity;
use App\Listeners\LogUserLoginLogout;
use App\Listeners\LogProjectActivity;

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
        ProjectCreated::class => [
            LogProjectActivity::class,
        ],
        ProjectUpdated::class => [
            LogProjectActivity::class,
        ],
        ProjectDeleted::class => [
            LogProjectActivity::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
