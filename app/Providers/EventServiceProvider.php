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
use App\Events\JabatanCreated;
use App\Events\JabatanUpdated;
use App\Events\JabatanDeleted;
use App\Events\FormasiCreated;
use App\Events\FormasiUpdated;
use App\Events\FormasiDeleted;
use App\Events\IzinCreated;
use App\Events\IzinUpdated;
use App\Events\IzinDeleted;
use App\Listeners\LogKaryawanActivity;
use App\Listeners\LogUserLoginLogout;
use App\Listeners\LogProjectActivity;
use App\Listeners\LogJabatanActivity;
use App\Listeners\LogFormasiActivity;
use App\Listeners\LogIzinActivity;

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
        JabatanCreated::class => [
            LogJabatanActivity::class,
        ],
        JabatanUpdated::class => [
            LogJabatanActivity::class,
        ],
        JabatanDeleted::class => [
            LogJabatanActivity::class,
        ],
        FormasiCreated::class => [
            LogFormasiActivity::class,
        ],
        FormasiUpdated::class => [
            LogFormasiActivity::class,
        ],
        FormasiDeleted::class => [
            LogFormasiActivity::class,
        ],
        IzinCreated::class => [
            LogIzinActivity::class,
        ],
        IzinUpdated::class => [
            LogIzinActivity::class,
        ],
        IzinDeleted::class => [
            LogIzinActivity::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
