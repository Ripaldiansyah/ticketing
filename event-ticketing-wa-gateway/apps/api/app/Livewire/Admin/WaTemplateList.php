<?php

namespace App\Livewire\Admin;

use App\Models\WaTemplate;
use Livewire\Component;
use Livewire\WithPagination;

class WaTemplateList extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $content = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'content' => 'required|string|max:1000',
    ];

    public function render()
    {
        $templates = WaTemplate::latest()->paginate(10);

        return view('livewire.admin.wa-template-list', compact('templates'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['editingId', 'name', 'content']);
        $this->content = "Halo {customer_name},\n\nTerima kasih atas pesanan Anda.\nSilakan download E-Ticket Anda di: {ticket_link}\n\nKode Tiket: {ticket_code}\nEvent: {event_name}";
        $this->showModal = true;
    }

    public function edit(int $id)
    {
        $template = WaTemplate::findOrFail($id);
        $this->editingId = $template->id;
        $this->name = $template->name;
        $this->content = $template->content;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'content' => $this->content,
        ];

        if ($this->editingId) {
            WaTemplate::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Template berhasil diupdate.');
        } else {
            WaTemplate::create($data);
            session()->flash('success', 'Template berhasil dibuat.');
        }

        $this->showModal = false;
        $this->reset(['editingId', 'name', 'content']);
    }

    public function delete(int $id)
    {
        WaTemplate::findOrFail($id)->delete();
        session()->flash('success', 'Template berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['editingId', 'name', 'content']);
    }
}
