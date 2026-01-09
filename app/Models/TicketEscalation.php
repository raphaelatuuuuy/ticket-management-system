<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketEscalation extends Model
{
    protected $fillable = [
        'ticket_id',
        'requested_by_id',
        'reason',
        'requested_at',
        'escalated_by_id',
        'escalated_at',
        'resolved_by_id',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'escalated_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the ticket that owns the escalation.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user who requested the escalation.
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_id')->withTrashed();
    }

    /**
     * Get the manager/admin who escalated to admin.
     */
    public function escalatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_by_id')->withTrashed();
    }

    /**
     * Get the admin who resolved the escalation.
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by_id')->withTrashed();
    }
}
