<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiRequest extends Model
{
    use SoftDeletes;

    protected $table = 'api_requests';

    protected $fillable = [
        'user_id',
        'request_id',
        'method',
        'endpoint',
        'ip_address',
        'user_agent',
        'request_headers',
        'request_payload',
        'status_code',
        'response_body',
        'response_time_ms',
        'status',
        'error_message',
        'processed_at',
    ];

    protected $casts = [
        'request_headers' => 'array',
        'request_payload' => 'array',
        'response_body' => 'array',
        'status_code' => 'integer',
        'response_time_ms' => 'integer',
        'is_active' => 'boolean',
        'processed_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
