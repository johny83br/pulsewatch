<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationChannel extends Model
{
    use HasUuids;

    public const TYPE_EMAIL = 'EMAIL';
    public const TYPE_TELEGRAM = 'TELEGRAM';
    public const TYPE_DISCORD = 'DISCORD';
    public const TYPE_WEBHOOK = 'WEBHOOK';

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'configuration',
        'enabled',
    ];

    protected function casts(): array
    {
        return [
            'configuration' => 'array',
            'enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class, 'channel_id');
    }
}
