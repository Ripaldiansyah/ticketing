<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class EventList extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $venue = '';
    public ?int $price = 0;
    public string $date_start = '';
    public string $date_end = '';
    public ?int $wa_account_id = null;
    public string $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'venue' => 'nullable|string|max:255',
        'price' => 'required|numeric|min:0',
        'date_start' => 'required|date',
        'date_end' => 'required|date|after_or_equal:date_start',
        'wa_account_id' => 'nullable|exists:wa_accounts,id',
    ];

    public function render()
    {
        $events = Event::with('waAccount')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('venue', 'like', "%{$this->search}%");
            })
            ->orderBy('date_start', 'desc')
            ->paginate(10);

        $waAccounts = \App\Models\WaAccount::where('is_active', true)->get();

        return view('livewire.admin.event-list', compact('events', 'waAccounts'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['editingId', 'name', 'venue', 'price', 'date_start', 'date_end', 'wa_account_id']);
        $this->showModal = true;
    }

    public function edit(int $id)
    {
        $event = Event::findOrFail($id);
        $this->editingId = $event->id;
        $this->name = $event->name;
        $this->venue = $event->venue ?? '';
        $this->price = $event->price;
        $this->date_start = $event->date_start->format('Y-m-d\TH:i');
        $this->date_end = $event->date_end->format('Y-m-d\TH:i');
        $this->wa_account_id = $event->wa_account_id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'venue' => $this->venue ?: null,
            'price' => $this->price,
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
            'wa_account_id' => $this->wa_account_id ?: null,
        ];

        if ($this->editingId) {
            Event::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Event berhasil diupdate.');
        } else {
            Event::create($data);
            session()->flash('success', 'Event berhasil dibuat.');
        }

        $this->showModal = false;
        $this->reset(['editingId', 'name', 'venue', 'price', 'date_start', 'date_end', 'wa_account_id']);
    }

    public function delete(int $id)
    {
        Event::findOrFail($id)->delete();
        session()->flash('success', 'Event berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['editingId', 'name', 'venue', 'date_start', 'date_end']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
