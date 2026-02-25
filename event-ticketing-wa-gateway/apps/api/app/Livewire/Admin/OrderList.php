<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\Order;
use App\Models\WaAccount;
use App\Models\WaTemplate;
use Livewire\Component;
use Livewire\WithPagination;

class OrderList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $eventFilter = '';
    public bool $showDetail = false;
    public ?Order $selectedOrder = null;

    // Persistent selections
    public ?int $selectedWaAccountId = null;
    public ?int $selectedTemplateId = null;

    // Create Modal
    public bool $showCreateModal = false;
    public $newEventId = '';
    public $newCustomerName = '';
    public $newPhone = '';
    public $newQty = 1;

    protected $rules = [
        'newEventId' => 'required|exists:events,id',
        'newCustomerName' => 'required|string|max:255',
        'newPhone' => 'required|string|regex:/^62[0-9]{9,13}$/',
        'newQty' => 'required|integer|min:1|max:100',
    ];

    public function render()
    {
        $orders = Order::with(['event', 'tickets'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('customer_name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
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

        // Fetch active events for dropdown (create modal)
        $activeEvents = Event::where('date_end', '>=', now())
            ->orderBy('date_start')
            ->get();

        // Fetch all events for filter
        $allEvents = Event::orderBy('name')->get();

        $waAccounts = WaAccount::where('is_active', true)->get();
        $templates = WaTemplate::latest()->get();

        return view('livewire.admin.order-list', compact('orders', 'allEvents', 'activeEvents', 'waAccounts', 'templates'))
            ->layout('layouts.app');
    }

    public function showOrderDetail(int $id)
    {
        $this->selectedOrder = Order::with(['event', 'tickets'])->findOrFail($id);
        $this->showDetail = true;
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->selectedOrder = null;
    }

    public function openCreateModal()
    {
        $this->reset(['newEventId', 'newCustomerName', 'newPhone', 'newQty']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function storeOrder()
    {
        $this->validate();

        $order = Order::create([
            'event_id' => $this->newEventId,
            'customer_name' => $this->newCustomerName,
            'phone' => $this->newPhone,
            'qty' => $this->newQty,
            'status' => 'UNPAID',
        ]);

        session()->flash('success', "Order #{$order->id} berhasil dibuat.");
        $this->closeCreateModal();
    }

    public function markAsPaid(int $id)
    {
        $order = Order::findOrFail($id);

        $order->markAsPaid(
            $this->selectedWaAccountId ?: null,
            $this->selectedTemplateId ?: null
        );

        session()->flash('success', "Order #{$id} berhasil di-PAID. {$order->qty} tiket telah digenerate dan WhatsApp dijadwalkan.");
        $this->closeDetail();
    }

    public function cancelOrder(int $id)
    {
        $order = Order::findOrFail($id);

        if ($order->status === 'PAID') {
            session()->flash('error', 'Tidak bisa cancel order yang sudah PAID.');
            return;
        }

        $order->update(['status' => 'CANCELLED']);
        session()->flash('success', "Order #{$id} berhasil dibatalkan.");
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
