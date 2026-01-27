<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PurgeOldAuditLogs extends Command
{
    protected $signature = 'audit:purge-old {days=30}';
    protected $description = 'Delete audit logs older than {days} days (default: 30)';

    public function handle()
    {
        $days = (int) $this->argument('days');
        $cutoff = Carbon::now()->subDays($days);
        $count = AuditLog::where('created_at', '<', $cutoff)->delete();
        $this->info("Deleted $count audit log(s) older than $days days.");
    }
}
