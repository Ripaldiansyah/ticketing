<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'order_id',
        'code',
        'token',
        'qr_path',
        'status',
        'checked_in_at',
        'checked_in_by_user_id',
        'rejected_at',
        'rejected_by_user_id',
        'reject_reason',
        'notes',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by_user_id');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by_user_id');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(TicketAudit::class)->orderBy('created_at', 'desc');
    }

    /**
     * Check in the ticket
     */
    public function checkIn(int $userId): void
    {
        $before = $this->only(['status', 'checked_in_at', 'checked_in_by_user_id']);

        $this->update([
            'status' => 'CHECKED_IN',
            'checked_in_at' => now(),
            'checked_in_by_user_id' => $userId,
        ]);

        $after = $this->only(['status', 'checked_in_at', 'checked_in_by_user_id']);

        $this->audits()->create([
            'user_id' => $userId,
            'action' => 'CHECK_IN',
            'payload' => ['before' => $before, 'after' => $after],
        ]);
    }

    /**
     * Reject the ticket with reason
     */
    public function reject(int $userId, string $reason): void
    {
        $before = $this->only(['status', 'rejected_at', 'rejected_by_user_id', 'reject_reason']);

        $this->update([
            'status' => 'REJECTED',
            'rejected_at' => now(),
            'rejected_by_user_id' => $userId,
            'reject_reason' => $reason,
        ]);

        $after = $this->only(['status', 'rejected_at', 'rejected_by_user_id', 'reject_reason']);

        $this->audits()->create([
            'user_id' => $userId,
            'action' => 'REJECT',
            'payload' => ['before' => $before, 'after' => $after, 'reason' => $reason],
        ]);
    }

    /**
     * Update customer data with audit
     */
    public function updateCustomer(int $userId, array $data): void
    {
        $order = $this->order;
        $before = $order->only(['customer_name', 'phone']);
        $before['notes'] = $this->notes;

        if (isset($data['customer_name'])) {
            $order->customer_name = $data['customer_name'];
        }
        if (isset($data['phone'])) {
            $order->phone = $data['phone'];
        }
        $order->save();

        if (isset($data['notes'])) {
            $this->notes = $data['notes'];
            $this->save();
        }

        $after = $order->only(['customer_name', 'phone']);
        $after['notes'] = $this->notes;

        $this->audits()->create([
            'user_id' => $userId,
            'action' => 'EDIT',
            'payload' => ['before' => $before, 'after' => $after],
        ]);
    }
}
