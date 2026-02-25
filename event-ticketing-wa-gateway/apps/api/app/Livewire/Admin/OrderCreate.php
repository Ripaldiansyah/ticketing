<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\Order;
use Livewire\Component;

class OrderCreate extends Component
{
    public string $event_id = '';
    public string $customer_name = '';
    public string $phone = '';
    public int $qty = 1;

    protected $rules = [
        'event_id' => 'required|exists:events,id',
        'customer_name' => 'required|string|max:255',
        'phone' => 'required|string|regex:/^62[0-9]{9,13}$/',
        'qty' => 'required|integer|min:1|max:100',
    ];

    protected $messages = [
        'phone.regex' => 'Phone harus format 62xxx (contoh: 6281234567890)',
    ];

    public function render()
    {
        $events = Event::where('date_end', '>=', now())
            ->orderBy('date_start')
            ->get();

        return view('livewire.admin.order-create', compact('events'))
            ->layout('layouts.app');
    }

    public function save()
    {
        $this->validate();

        $order = Order::create([
            'event_id' => $this->event_id,
            'customer_name' => $this->customer_name,
            'phone' => $this->phone,
            'qty' => $this->qty,
            'status' => 'UNPAID',
        ]);

        session()->flash('success', "Order #{$order->id} berhasil dibuat.");

        return redirect()->route('admin.orders');
    }
}
