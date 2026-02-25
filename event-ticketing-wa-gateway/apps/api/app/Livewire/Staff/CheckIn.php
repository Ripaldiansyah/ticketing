<?php

namespace App\Livewire\Staff;

use App\Models\Ticket;
use Livewire\Attributes\On;
use Livewire\Component;

class CheckIn extends Component
{
    public string $tokenInput = '';
    public ?Ticket $ticket = null;
    public bool $showEditModal = false;
    public bool $showRejectModal = false;

    // Edit form
    public string $editCustomerName = '';
    public string $editPhone = '';
    public string $editNotes = '';

    // Reject form
    public string $rejectReason = '';

    // Toast messages
    public string $toastMessage = '';
    public string $toastType = ''; // success, error, warning

    protected $rules = [
        'editCustomerName' => 'required|string|max:255',
        'editPhone' => 'required|string|regex:/^62[0-9]{9,13}$/',
        'editNotes' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        // Auto focus is handled by JS
    }

    public function render()
    {
        return view('livewire.staff.check-in')
            ->layout('layouts.app');
    }

    public function updatedTokenInput()
    {
        // Debounce is handled by wire:model.debounce.150ms
        // Reduced length check because Ticket Code is shorter than Token
        if (strlen($this->tokenInput) >= 5) {
            $this->lookupTicket();
        }
    }

    public function lookupTicket()
    {
        $token = trim($this->tokenInput);

        if (empty($token)) {
            return;
        }

        $this->ticket = Ticket::with(['event', 'order', 'checkedInBy', 'rejectedBy'])
            ->where(function ($q) use ($token) {
                $q->where('code', $token)
                    ->orWhere('token', $token);
            })
            ->first();

        if (!$this->ticket) {
            $this->showToast('Token tidak ditemukan!', 'error');
            $this->clearAndFocus();
            return;
        }

        // Prepare edit form data
        $this->editCustomerName = $this->ticket->order->customer_name;
        $this->editPhone = $this->ticket->order->phone;
        $this->editNotes = $this->ticket->notes ?? '';
    }

    public function approve()
    {
        if (!$this->ticket) {
            $this->showToast('Tidak ada tiket yang dipilih!', 'error');
            return;
        }

        if ($this->ticket->status === 'CHECKED_IN') {
            $this->showToast('Tiket sudah di check-in sebelumnya!', 'warning');
            return;
        }

        if ($this->ticket->status === 'REJECTED') {
            $this->showToast('Tiket sudah ditolak, tidak bisa approve!', 'warning');
            return;
        }

        $this->ticket->checkIn(auth()->id());
        $this->showToast('Check-in berhasil! ✓', 'success');
        $this->clearAndFocus();
    }

    public function openEditModal()
    {
        if (!$this->ticket) return;

        $this->editCustomerName = $this->ticket->order->customer_name;
        $this->editPhone = $this->ticket->order->phone;
        $this->editNotes = $this->ticket->notes ?? '';
        $this->showEditModal = true;
    }

    public function saveEdit()
    {
        $this->validate([
            'editCustomerName' => 'required|string|max:255',
            'editPhone' => 'required|string|regex:/^62[0-9]{9,13}$/',
            'editNotes' => 'nullable|string|max:1000',
        ]);

        if (!$this->ticket) return;

        $this->ticket->updateCustomer(auth()->id(), [
            'customer_name' => $this->editCustomerName,
            'phone' => $this->editPhone,
            'notes' => $this->editNotes,
        ]);

        // Refresh ticket data
        $this->ticket->refresh();
        $this->ticket->load('order');

        $this->showEditModal = false;
        $this->showToast('Data customer berhasil diupdate!', 'success');
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
    }

    public function openRejectModal()
    {
        if (!$this->ticket) return;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function submitReject()
    {
        $this->validate([
            'rejectReason' => 'required|string|min:5|max:500',
        ], [
            'rejectReason.required' => 'Alasan penolakan wajib diisi!',
            'rejectReason.min' => 'Alasan minimal 5 karakter.',
        ]);

        if (!$this->ticket) return;

        if ($this->ticket->status === 'CHECKED_IN') {
            $this->showToast('Tiket sudah di check-in, tidak bisa ditolak!', 'warning');
            $this->showRejectModal = false;
            return;
        }

        $this->ticket->reject(auth()->id(), $this->rejectReason);
        $this->showRejectModal = false;
        $this->showToast('Tiket berhasil ditolak.', 'success');
        $this->clearAndFocus();
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectReason = '';
    }

    private function showToast(string $message, string $type)
    {
        $this->toastMessage = $message;
        $this->toastType = $type;

        $this->dispatch('show-toast');
    }

    private function clearAndFocus()
    {
        $this->tokenInput = '';
        $this->ticket = null;
        $this->dispatch('focus-input');
    }

    public function clearTicket()
    {
        $this->ticket = null;
        $this->tokenInput = '';
        $this->dispatch('focus-input');
    }
}
