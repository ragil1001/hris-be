<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Jobs\LogAuditJob;

class LogAuthentication
{
    public function handle($event)
    {
        $user = $event->user;
        $ip = request()->ip();
        $userAgent = request()->userAgent();
        
        if ($event instanceof Login) {
            LogAuditJob::dispatch($user->id, 'Login', $ip, $userAgent, ['email' => $user->email]);
        } elseif ($event instanceof Logout) {
            // User might be null in some logout contexts depending on guard, but normally available
            $userId = $user ? $user->id : null;
            LogAuditJob::dispatch($userId, 'Logout', $ip, $userAgent, null);
        }
    }
}
