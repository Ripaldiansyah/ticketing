<div class="min-h-screen bg-slate-50 py-6" x-data="{ init() { $refs.tokenInput.focus() } }" x-init="init()">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Scanner Area -->

        <!-- Toast Notification -->
        @if($toastMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
            @show-toast.window="show = true; setTimeout(() => show = false, 3000)" class="fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white font-medium
                    @if($toastType === 'success') bg-green-600
                    @elseif($toastType === 'error') bg-red-600
                    @else bg-yellow-600 @endif">
            {{ $toastMessage }}
        </div>
        @endif

        <!-- Token Input -->
        <div class="bg-white shadow rounded-xl p-6 mb-6 border border-gray-100">
            <label class="block text-sm font-medium text-gray-700 mb-2">Scan atau Ketik Token</label>
            <input type="text" wire:model.live.debounce.150ms="tokenInput" wire:keydown.enter="lookupTicket"
                x-ref="tokenInput" @focus-input.window="$refs.tokenInput.focus(); $refs.tokenInput.value = ''"
                class="w-full text-2xl font-mono px-4 py-4 border-2 border-indigo-500 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 text-center tracking-wider placeholder-gray-300"
                placeholder="Scan QR Code..." autofocus autocomplete="off">
            <p class="text-sm text-gray-400 mt-2 text-center">
                Arahkan scanner ke input, atau ketik token manual lalu tekan Enter
            </p>
        </div>

        <!-- Ticket Result Panel -->
        @if($ticket)
        <div class="bg-white shadow rounded-xl overflow-hidden border border-gray-100">
            <!-- Status Header -->
            <div class="px-6 py-4 
                    @if($ticket->status === 'ISSUED') bg-blue-500
                    @elseif($ticket->status === 'CHECKED_IN') bg-green-500
                    @else bg-red-500 @endif">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white">
                        @if($ticket->status === 'ISSUED')
                        ✓ Tiket Valid - Siap Check-In
                        @elseif($ticket->status === 'CHECKED_IN')
                        ⚠️ SUDAH CHECK-IN
                        @else
                        ❌ TIKET DITOLAK
                        @endif
                    </h2>
                    <span class="text-white text-sm">{{ $ticket->code }}</span>
                </div>
            </div>

            <!-- Ticket Info -->
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Event</label>
                        <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $ticket->event->name }}</p>
                        <p class="text-sm text-gray-500">{{ $ticket->event->date_start->format('d M Y H:i') }}</p>
                        @if($ticket->event->venue)
                        <p class="text-sm text-gray-500">📍 {{ $ticket->event->venue }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Customer</label>
                        <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $ticket->order->customer_name
                            }}</p>
                        <p class="text-sm text-gray-500">📱 {{ $ticket->order->phone }}</p>
                    </div>
                </div>

                @if($ticket->notes)
                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <label class="text-sm text-yellow-600 dark:text-yellow-400">Notes</label>
                    <p class="text-gray-900 dark:text-white">{{ $ticket->notes }}</p>
                </div>
                @endif

                @if($ticket->status === 'CHECKED_IN')
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border-2 border-green-500">
                    <p class="text-green-700 dark:text-green-400 font-medium">
                        ✓ Sudah check-in pada {{ $ticket->checked_in_at?->format('d M Y H:i:s') }}
                    </p>
                    <p class="text-green-600 text-sm">oleh {{ $ticket->checkedInBy?->name }}</p>
                </div>
                @endif

                @if($ticket->status === 'REJECTED')
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border-2 border-red-500">
                    <p class="text-red-700 dark:text-red-400 font-medium">
                        ❌ Ditolak pada {{ $ticket->rejected_at?->format('d M Y H:i:s') }}
                    </p>
                    <p class="text-red-600 text-sm">oleh {{ $ticket->rejectedBy?->name }}</p>
                    <p class="text-red-800 dark:text-red-300 mt-2">
                        <strong>Alasan:</strong> {{ $ticket->reject_reason }}
                    </p>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3 pt-4 border-t dark:border-gray-700">
                    @if($ticket->status === 'ISSUED')
                    <button wire:click="approve"
                        class="flex-1 px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-lg font-bold rounded-xl transition transform hover:scale-105">
                        ✓ APPROVE CHECK-IN
                    </button>
                    <button wire:click="openEditModal"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition">
                        ✏️ Edit Data
                    </button>
                    <button wire:click="openRejectModal"
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition">
                        ❌ Reject
                    </button>
                    @else
                    <button wire:click="clearTicket"
                        class="flex-1 px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-xl transition">
                        Scan Tiket Lain
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Edit Modal -->
    @if($showEditModal)
    <div
        class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit Data Customer</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Customer</label>
                    <input type="text" wire:model="editCustomerName"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('editCustomerName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor HP (format
                        62xxx)</label>
                    <input type="text" wire:model="editPhone"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('editPhone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                    <textarea wire:model="editNotes" rows="3"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    @error('editNotes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button wire:click="closeEditModal"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Batal
                </button>
                <button wire:click="saveEdit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Simpan
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Reject Modal -->
    @if($showRejectModal)
    <div
        class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-bold text-red-600 mb-4">❌ Reject Tiket</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alasan Penolakan
                    (wajib)</label>
                <textarea wire:model="rejectReason" rows="4"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Jelaskan alasan penolakan..."></textarea>
                @error('rejectReason') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button wire:click="closeRejectModal"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Batal
                </button>
                <button wire:click="submitReject" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Konfirmasi Reject
                </button>
            </div>
        </div>
    </div>
    @endif
</div>