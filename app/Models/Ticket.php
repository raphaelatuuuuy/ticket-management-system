<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'title',
        'description',
        'status_id',
        'priority_id',
        'category_id',
        'user_id',
        'assigned_to',
        'resolved_at',
        'sla_due_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'sla_due_at' => 'datetime',
    ];

    // Add accessor for status text (for backward compatibility)
    public function getStatusAttribute()
    {
        return $this->status_relation ? $this->status_relation->description : 'Open';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to')->withTrashed();
    }

    public function escalationRequestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalation_requested_by')->withTrashed();
    }

    public function status_relation(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function priority_relation(): BelongsTo
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    public function category_relation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at', 'asc');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)->whereNull('comment_id');
    }

    public function escalations(): HasMany
    {
        return $this->hasMany(TicketEscalation::class)->orderBy('requested_at', 'desc');
    }

    public function latestEscalation()
    {
        return $this->hasOne(TicketEscalation::class)->latestOfMany('requested_at');
    }

    public function reopenRequests(): HasMany
    {
        return $this->hasMany(ReopenRequest::class)->orderBy('requested_at', 'desc');
    }

    public function latestReopenRequest()
    {
        return $this->hasOne(ReopenRequest::class)->latestOfMany('requested_at');
    }
}
