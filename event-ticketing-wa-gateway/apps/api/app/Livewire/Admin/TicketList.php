<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination;

class TicketList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $eventFilter = '';
    public bool $showDetail = false;
    public ?Ticket $selectedTicket = null;

    public function render()
    {
        $tickets = Ticket::with(['event', 'order', 'checkedInBy', 'rejectedBy', 'audits.user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', "%{$this->search}%")
                        ->orWhereHas('order', function ($orderQuery) {
                            $orderQuery->where('customer_name', 'like', "%{$this->search}%")
                                ->orWhere('phone', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->eventFilter, function ($query) {
                $query->where('event_id', $this->eventFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $events = Event::orderBy('name')->get();

        return view('livewire.admin.ticket-list', compact('tickets', 'events'))
            ->layout('layouts.app');
    }

    public function showTicketDetail(int $id)
    {
        $this->selectedTicket = Ticket::with(['event', 'order', 'checkedInBy', 'rejectedBy', 'audits.user'])->findOrFail($id);
        $this->showDetail = true;
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->selectedTicket = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingEventFilter()
    {
        $this->resetPage();
    }
}