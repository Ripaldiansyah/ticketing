<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Flash Messages -->
        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
        @endif
        @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        @if($activeTab === 'accounts')
        <!-- Account List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-end mb-6">
                    <button wire:click="$set('showCreateModal', true)"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition shadow-sm">
                        + Tambah Akun Baru
                    </button>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Name</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Number</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Session ID</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($accounts as $account)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $account->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                    {{ $account->number ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono text-xs">
                                    {{ $account->session_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($account->is_active)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-green-400 rounded-full"></span> Active
                                    </span>
                                    @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-gray-400 rounded-full"></span> Inactive
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button wire:click="openScan({{ $account->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 font-medium">Connect
                                        / Scan</button>
                                    <button wire:click="deleteAccount({{ $account->id }})"
                                        wire:confirm="Yakin menghapus akun ini? Session akan diputus."
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 font-medium ml-2">Delete</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <p>Belum ada akun WhatsApp terdaftar.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        @if($showCreateModal)
        <div
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Akun WhatsApp Baru</h3>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Akun</label>
                    <input type="text" wire:model="newAccountName"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="Contoh: Admin Utama">
                    @error('newAccountName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-2">
                    <button wire:click="$set('showCreateModal', false)"
                        class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Batal</button>
                    <button wire:click="createAccount"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Simpan</button>
                </div>
            </div>
        </div>
        @endif

        @elseif($activeTab === 'scan' && $selectedAccount)
        <!-- Scan / Detail View -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <div class="flex items-center space-x-4">
                        <button wire:click="$set('activeTab', 'accounts')" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </button>
                        <h2 class="text-xl font-bold text-gray-900">{{ $selectedAccount->name }}</h2>
                    </div>
                    <span class="text-sm text-gray-500 font-mono">{{ $selectedAccount->session_id }}</span>
                </div>

                <div class="flex flex-col items-center justify-center space-y-6" wire:poll.2000ms="checkStatus">

                    @if($scanStatus['connected'] ?? false)
                    <div
                        class="flex flex-col items-center p-8 bg-green-50 rounded-lg border border-green-200 w-full max-w-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500 mb-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-xl font-bold text-green-700 mb-2">Connected</h3>
                        <p class="text-green-600 text-center">
                            WhatsApp terhubung.<br>
                            @if(isset($scanStatus['info']))
                            <span class="font-semibold text-lg">{{ $scanStatus['info']['pushname'] ?? '' }} ({{
                                $scanStatus['info']['wid']['user'] ?? '' }})</span>
                            @endif
                        </p>

                        <button wire:click="logout({{ $selectedAccount->id }})" wire:loading.attr="disabled"
                            class="mt-8 px-6 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-500 transition">
                            <span wire:loading.remove wire:target="logout">Disconnect / Logout</span>
                            <span wire:loading wire:target="logout">Logging out...</span>
                        </button>
                    </div>
                    @else
                    <div class="flex flex-col items-center w-full max-w-md">
                        <h3 class="text-lg font-medium text-gray-700 mb-4">Scan QR Code</h3>

                        @if($qrCode)
                        <div class="bg-white p-4 rounded-lg shadow-md border mb-4">
                            {!! QrCode::size(250)->generate($qrCode) !!}
                        </div>
                        <p class="text-sm text-gray-500 animate-pulse">Buka WhatsApp > Linked Devices > Link a Device
                        </p>
                        @elseif(isset($scanStatus['error']))
                        <div
                            class="flex flex-col items-center justify-center p-8 bg-red-50 rounded-lg border border-red-200 w-full">
                            <svg class="h-10 w-10 text-red-400 mb-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                            <p class="text-red-600 font-medium text-center">WA Gateway tidak bisa diakses</p>
                            <p class="text-red-400 text-sm mt-1 text-center">Pastikan wa-gateway sedang berjalan (PM2)
                            </p>
                            <button wire:click="checkStatus"
                                class="mt-4 px-4 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm font-medium">
                                🔄 Coba Lagi
                            </button>
                        </div>
                        @else
                        <div
                            class="flex flex-col items-center justify-center p-12 bg-gray-50 rounded-lg border border-dashed border-gray-300 w-full h-64">
                            <svg class="animate-spin h-8 w-8 text-gray-400 mb-4" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <p class="text-gray-500">Menunggu QR Code dari Gateway...</p>
                            <p class="text-xs text-gray-400 mt-2">Pastikan WA Gateway berjalan.</p>
                        </div>
                        @endif
                    </div>
                    @endif

                </div>
            </div>
        </div>
        @endif
    </div>
</div>