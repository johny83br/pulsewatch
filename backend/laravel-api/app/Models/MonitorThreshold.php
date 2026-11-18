<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitorThreshold extends Model
{
    use HasUuids;

    protected $fillable = [
        'monitor_id',
        'failure_threshold',
        'recovery_threshold',
        'response_time_threshold_ms',
    ];

    protected function casts(): array
    {
        return [
            'failure_threshold' => 'integer',
            'recovery_threshold' => 'integer',
            'response_time_threshold_ms' => 'integer',
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
