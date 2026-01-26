<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use App\Jobs\LogAuditJob;

class LogUserLoginLogout
{
    public function handle($event)
    {
        $user = $event->user;
        $ip = $event->ip;
        $userAgent = $event->userAgent;
        $type = $event instanceof UserLoggedIn ? 'login' : 'logout';
        LogAuditJob::dispatch($user->id, $type, $ip, $userAgent);
    }
}
