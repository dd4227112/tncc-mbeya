<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Webhook extends Model
{
    use SoftDeletes;

    protected $table = 'webhooks';

    protected $fillable = [
        'user_id',
        'name',
        'event',
        'url',
        'secret',
        'headers',
        'is_active',
        'retry_count',
        'last_sent_at',
    ];

    protected $casts = [
        'headers' => 'array',
        'is_active' => 'boolean',
        'retry_count' => 'integer',
        'last_sent_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
