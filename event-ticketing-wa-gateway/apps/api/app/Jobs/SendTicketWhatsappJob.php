<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\QrService;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTicketWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The backoff strategy for retrying the job.
     */
    public array $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $ticketId,
        public ?int $overrideWaAccountId = null,
        public ?int $templateId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService, QrService $qrService): void
    {
        $ticket = Ticket::with(['order', 'event.waAccount'])->find($this->ticketId);

        if (!$ticket) {
            Log::error('SendTicketWhatsappJob: Ticket not found', ['ticket_id' => $this->ticketId]);
            return;
        }

        if (!$ticket->qr_path) {
            Log::error('SendTicketWhatsappJob: QR path not set', ['ticket_id' => $this->ticketId]);
            return;
        }

        $order = $ticket->order;
        $event = $ticket->event;

        // Build Caption
        $caption = "";

        // 1. Try Custom Template
        if ($this->templateId) {
            $template = \App\Models\WaTemplate::find($this->templateId);
            if ($template) {
                $caption = $template->content;
                // Replace Variables
                $vars = [
                    '{customer_name}' => $order->customer_name,
                    '{event_name}' => $event->name,
                    '{event_date}' => $event->date_start->format('d M Y H:i'),
                    '{event_venue}' => $event->venue ?? '-',
                    '{ticket_code}' => $ticket->code,
                    '{ticket_link}' => asset('storage/' . $ticket->qr_path), // Or direct link if applicable
                    '{qty}' => $order->qty,
                ];

                foreach ($vars as $key => $val) {
                    $caption = str_replace($key, $val, $caption);
                }
            }
        }

        // 2. Default Caption if empty
        if (empty($caption)) {
            $caption = "🎫 *E-TICKET*\n\n";
            $caption .= "Event: {$event->name}\n";
            $caption .= "Tanggal: " . $event->date_start->format('d M Y H:i') . "\n";
            if ($event->venue) {
                $caption .= "Venue: {$event->venue}\n";
            }
            $caption .= "\n";
            $caption .= "Nama: {$order->customer_name}\n";
            $caption .= "Kode Tiket: {$ticket->code}\n";
            $caption .= "\n";
            $caption .= "Tunjukkan QR Code ini saat check-in.\n";
            $caption .= "Jangan bagikan QR ini kepada orang lain.";
        }

        try {
            $base64 = $qrService->getBase64($ticket->qr_path);

            $waAccount = null;

            // 1. Override from Job (User explicit selection)
            if ($this->overrideWaAccountId) {
                $waAccount = \App\Models\WaAccount::find($this->overrideWaAccountId);
            }

            // 2. Event Assigned Account
            if (!$waAccount) {
                $waAccount = $event->waAccount;
            }

            // 3. Fallback: First active account
            if (!$waAccount) {
                $waAccount = \App\Models\WaAccount::where('is_active', true)->first();
            }

            if (!$waAccount) {
                // Check if account is active or exists
                throw new \Exception("No active WhatsApp Account found. Please add one in Settings.");
            }

            $sessionId = $waAccount->session_id;

            $whatsAppService->sendImage(
                to: $order->phone,
                caption: $caption,
                filename: "{$ticket->code}.png",
                mime: 'image/png',
                base64: $base64,
                sessionId: $sessionId
            );

            Log::info('Ticket WhatsApp sent', [
                'ticket_id' => $ticket->id,
                'phone' => $order->phone,
                'session_id' => $sessionId,
            ]);
        } catch (\Exception $e) {
            Log::error('SendTicketWhatsappJob failed', [
                'ticket_id' => $this->ticketId,
                'phone' => $order->phone,
                'error' => $e->getMessage(),
            ]);

            throw $e; // Re-throw to trigger retry
        }
    }
}
