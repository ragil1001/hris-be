<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'auth.audit_logs';
    protected $guarded = [];
    protected $casts = [
        'meta' => 'array',
    ];
}
