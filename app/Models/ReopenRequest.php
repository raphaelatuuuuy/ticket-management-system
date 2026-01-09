<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReopenRequest extends Model
{
    protected $fillable = [
        'ticket_id',
        'requested_by_id',
        'reason',
        'status',
        'responded_by_id',
        'requested_at',
        'responded_at',
        'remarks',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the ticket that owns the reopen request.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user who requested the reopen.
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_id')->withTrashed();
    }

    /**
     * Get the user who responded to the reopen request.
     */
    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by_id')->withTrashed();
    }
}
