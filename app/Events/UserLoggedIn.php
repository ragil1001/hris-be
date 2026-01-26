<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoggedIn
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $ip;
    public $userAgent;

    public function __construct(User $user, $ip = null, $userAgent = null)
    {
        $this->user = $user;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
    }
}
