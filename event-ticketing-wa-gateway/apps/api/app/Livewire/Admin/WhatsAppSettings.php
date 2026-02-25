<?php

namespace App\Livewire\Admin;

use App\Models\WaAccount;
use App\Services\WhatsAppService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

#[Layout('layouts.app')]
class WhatsAppSettings extends Component
{
    public $accounts = [];
    public $newAccountName = '';
    public $showCreateModal = false;
    public $activeTab = 'accounts'; // accounts, scan

    // Scan state
    public $selectedAccount = null;
    public $scanStatus = [];
    public $qrCode = null;

    public function mount()
    {
        $this->refreshAccounts();
    }

    public function refreshAccounts()
    {
        $this->accounts = WaAccount::all();
    }

    public function createAccount(WhatsAppService $service)
    {
        $this->validate([
            'newAccountName' => 'required|min:3|max:50',
        ]);

        $sessionId = Str::uuid()->toString();

        // Call Gateway to init session
        $res = $service->startSession($sessionId);

        if (isset($res['error'])) {
            session()->flash('error', 'Gateway Error: ' . $res['error']);
            return;
        }

        WaAccount::create([
            'name' => $this->newAccountName,
            'session_id' => $sessionId,
            'is_active' => true,
        ]);

        $this->newAccountName = '';
        $this->showCreateModal = false;
        $this->refreshAccounts();
        session()->flash('success', 'Akun berhasil dibuat. Silakan scan QR.');
    }

    public function openScan($accountId, WhatsAppService $service)
    {
        $this->selectedAccount = WaAccount::find($accountId);
        if (!$this->selectedAccount) return;

        // Ensure session started in gateway (idempotent)
        $service->startSession($this->selectedAccount->session_id);

        $this->activeTab = 'scan';
        $this->checkStatus($service);
    }

    public function checkStatus(WhatsAppService $service)
    {
        if (!$this->selectedAccount) return;

        $status = $service->getStatus($this->selectedAccount->session_id);

        $this->scanStatus = $status;
        $this->qrCode = $status['qr'] ?? null;

        // Update number if connected and we have info
        if (($status['connected'] ?? false) && isset($status['info'])) {
            if (!$this->selectedAccount->number) {
                $this->selectedAccount->update([
                    'number' => $status['info']['wid']['user'] ?? null
                ]);
            }
        }
    }

    public function logout($accountId, WhatsAppService $service)
    {
        $account = WaAccount::find($accountId);
        if (!$account) return;

        if ($service->logout($account->session_id)) {
            session()->flash('success', 'Logout berhasil based on gateway.');
        } else {
            session()->flash('error', 'Logout gagal atau sudah logout.');
        }

        // Gateway might take time to reuse session id for new QR, create new session logic?
        // Actually logout keeps session alive but logged out (needs re-scan)
    }

    public function deleteAccount($accountId, WhatsAppService $service)
    {
        $account = WaAccount::find($accountId);
        if (!$account) return;

        $service->deleteSession($account->session_id);
        $account->delete();

        $this->refreshAccounts();
        if ($this->selectedAccount?->id === $accountId) {
            $this->selectedAccount = null;
            $this->activeTab = 'accounts';
        }
    }

    public function render()
    {
        return view('livewire.admin.whats-app-settings');
    }
}
