<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Monitor extends Model
{
    use HasUuids;
    use SoftDeletes;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_PAUSED = 'PAUSED';

    public const STATE_UNKNOWN = 'UNKNOWN';
    public const STATE_UP = 'UP';
    public const STATE_DOWN = 'DOWN';
    public const STATE_DEGRADED = 'DEGRADED';

    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_HEAD = 'HEAD';
    public const METHOD_OPTIONS = 'OPTIONS';

    protected $fillable = [
        'user_id',
        'group_id',
        'name',
        'description',
        'url',
        'method',
        'headers',
        'request_body',
        'expected_status_codes',
        'status',
        'interval_seconds',
        'timeout_ms',
    ];

    protected function casts(): array
    {
        return [
            'headers' => 'array',
            'request_body' => 'array',
            'expected_status_codes' => 'array',
            'interval_seconds' => 'integer',
            'timeout_ms' => 'integer',
            'consecutive_failures' => 'integer',
            'consecutive_successes' => 'integer',
            'next_check_at' => 'immutable_datetime',
            'last_check_at' => 'immutable_datetime',
            'deleted_at' => 'immutable_datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(MonitorGroup::class, 'group_id');
    }

    public function threshold(): HasOne
    {
        return $this->hasOne(MonitorThreshold::class);
    }

    public function checks(): HasMany
    {
        return $this->hasMany(MonitorCheck::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }
}
