<?php

namespace App\Models;

use App\Jobs\SendTicketWhatsappJob;
use App\Services\QrService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'customer_name',
        'phone',
        'qty',
        'status',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Mark order as PAID, generate tickets and dispatch WA jobs
     */
    public function markAsPaid(?int $waAccountId = null, ?int $templateId = null): void
    {
        if ($this->status === 'PAID') {
            return;
        }

        $this->update(['status' => 'PAID']);

        $qrService = app(QrService::class);

        for ($i = 0; $i < $this->qty; $i++) {
            $code = strtoupper(Str::random(8)) . '-' . $this->id . '-' . ($i + 1);
            $token = Str::random(40);

            $ticket = $this->tickets()->create([
                'event_id' => $this->event_id,
                'code' => $code,
                'token' => $token,
                'status' => 'ISSUED',
            ]);

            // Generate QR code
            $qrPath = $qrService->generate($ticket->code, $ticket->token);
            $ticket->update(['qr_path' => $qrPath]);

            // Dispatch WhatsApp job
            // Using ::dispatch($ticketId, $waAccountId, $templateId)
            SendTicketWhatsappJob::dispatch($ticket->id, $waAccountId, $templateId);
        }
    }
}
