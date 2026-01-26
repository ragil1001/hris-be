<?php

namespace App\Jobs;

use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogAuditJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $event;
    public $ip;
    public $userAgent;
    public $meta;

    public function __construct($userId, $event, $ip = null, $userAgent = null, $meta = null)
    {
        $this->userId = $userId;
        $this->event = $event;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->meta = $meta;
    }

    public function handle(): void
    {
        AuditLog::create([
            'user_id' => $this->userId,
            'event' => $this->event,
            'ip_address' => $this->ip,
            'user_agent' => $this->userAgent,
            'meta' => $this->meta,
        ]);
    }
}
