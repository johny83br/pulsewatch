<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitorCheck extends Model
{
    use HasUuids;

    public const EXECUTION_PENDING = 'PENDING';
    public const EXECUTION_COMPLETED = 'COMPLETED';
    public const EXECUTION_EXPIRED = 'EXPIRED';

    protected $fillable = [
        'monitor_id',
        'execution_status',
        'success',
        'status_code',
        'response_time_ms',
        'error_type',
        'error_message',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'status_code' => 'integer',
            'response_time_ms' => 'integer',
            'checked_at' => 'immutable_datetime',
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
