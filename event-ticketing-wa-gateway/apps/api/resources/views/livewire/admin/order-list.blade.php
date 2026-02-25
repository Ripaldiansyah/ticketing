<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
        @endif

        <!-- Filters Bar -->
        <div
            class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <!-- Search & Dropdowns -->
                <div class="flex flex-wrap gap-3 w-full md:w-auto flex-1 items-center">
                    <!-- Create Button -->
                    <button wire:click="openCreateModal"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg font-medium transition shadow-sm flex items-center text-sm whitespace-nowrap">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        New Order
                    </button>

                    <div class="h-6 border-l border-gray-200 mx-1"></div>
                    <div class="relative w-full md:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama/phone..."
                            class="pl-9 w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <select wire:model.live="statusFilter"
                        class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                        <option value="">Status: Semua</option>
                        <option value="UNPAID">Unpaid</option>
                        <option value="PAID">Paid</option>
                        <option value="CANCELLED">Cancelled</option>
                    </select>

                    <select wire:model.live="eventFilter"
                        class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer max-w-[150px]">
                        <option value="">Event: Semua</option>
                        @foreach($allEvents as $event)
                        <option value="{{ $event->id }}">{{ Str::limit($event->name, 20) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Settings (WA & Template) -->
                <div class="flex flex-wrap gap-3 w-full md:w-auto justify-end border-l border-gray-100 pl-4">
                    <!-- Global WA Account Selector -->
                    <div class="flex items-center space-x-2 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                        </svg>
                        <select wire:model="selectedWaAccountId"
                            class="border-none bg-transparent text-xs focus:ring-0 text-blue-800 font-bold p-0 pl-1 pr-6 cursor-pointer w-24">
                            <option value="">Auto (Default)</option>
                            @foreach($waAccounts as $acc)
                            <option value="{{ $acc->id }}">{{ Str::limit($acc->name, 10) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Global Template Selector -->
                    <div
                        class="flex items-center space-x-2 bg-purple-50 px-3 py-1.5 rounded-lg border border-purple-100">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                            </path>
                        </svg>
                        <select wire:model="selectedTemplateId"
                            class="border-none bg-transparent text-xs focus:ring-0 text-purple-800 font-bold p-0 pl-1 pr-6 cursor-pointer w-24">
                            <option value="">Default Tmpl</option>
                            @foreach($templates as $tmpl)
                            <option value="{{ $tmpl->id }}">{{ Str::limit($tmpl->name, 10) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div
            class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            ID</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Event</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Customer</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Qty</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            #{{ $order->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                            {{ Str::limit($order->event->name, 30) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="text-gray-900 dark:text-white font-medium">{{ $order->customer_name }}</div>
                            <div class="text-gray-500 text-xs">{{ $order->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                            {{ $order->qty }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($order->status === 'UNPAID')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <span class="w-1.5 h-1.5 mr-1.5 bg-yellow-400 rounded-full"></span>
                                UNPAID
                            </span>
                            @elseif($order->status === 'PAID')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="w-1.5 h-1.5 mr-1.5 bg-green-400 rounded-full"></span>
                                PAID
                            </span>
                            @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <span class="w-1.5 h-1.5 mr-1.5 bg-gray-400 rounded-full"></span>
                                CANCELLED
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <button wire:click="showOrderDetail({{ $order->id }})"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 font-medium">Detail</button>
                            @if($order->status === 'UNPAID')
                            <button wire:click="markAsPaid({{ $order->id }})" wire:confirm="Yakin mark as PAID?"
                                class="text-green-600 hover:text-green-900 dark:text-green-400 font-medium">Mark
                                PAID</button>
                            <button wire:click="cancelOrder({{ $order->id }})" wire:confirm="Yakin cancel order ini?"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 font-medium">Cancel</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <p>Tidak ada order.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Detail Modal -->
    @if($showDetail && $selectedOrder)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 transition-opacity"
        wire:click.self="closeDetail">
        <div
            class="relative top-10 mx-auto p-0 border w-full max-w-2xl shadow-xl rounded-xl bg-white dark:bg-gray-800 overflow-hidden transform transition-all">
            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-600 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Order #{{ $selectedOrder->id }}
                </h3>
                <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <!-- Order Info Grid -->
                <div class="grid grid-cols-2 gap-x-6 gap-y-4 mb-6">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Event</label>
                        <p class="font-bold text-gray-900 dark:text-white">{{ $selectedOrder->event->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
                        <p class="mt-1">
                            @if($selectedOrder->status === 'UNPAID')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">UNPAID</span>
                            @elseif($selectedOrder->status === 'PAID')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">PAID</span>
                            @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">CANCELLED</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Customer</label>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $selectedOrder->customer_name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Phone</label>
                        <p class="font-medium text-gray-900 dark:text-white font-mono">{{ $selectedOrder->phone }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Qty</label>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $selectedOrder->qty }} Tiket</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Created</label>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $selectedOrder->created_at->format('d M
                            Y H:i') }}</p>
                    </div>
                </div>

                <!-- Tickets Section -->
                @if($selectedOrder->tickets->count() > 0)
                <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                    <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                            </path>
                        </svg>
                        Generated Tickets ({{ $selectedOrder->tickets->count() }})
                    </h4>
                    <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                        @foreach($selectedOrder->tickets as $ticket)
                        <div
                            class="flex justify-between items-center p-2.5 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                            <span class="text-sm font-mono font-medium text-gray-700 dark:text-white">{{ $ticket->code
                                }}</span>
                            <span class="px-2 py-1 text-xs font-bold rounded 
                                            @if($ticket->status === 'ISSUED') bg-blue-50 text-blue-700 border border-blue-100
                                            @elseif($ticket->status === 'CHECKED_IN') bg-green-50 text-green-700 border border-green-100
                                            @else bg-red-50 text-red-700 border border-red-100 @endif">
                                {{ $ticket->status }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-100 dark:border-gray-600 flex justify-end space-x-3">
                @if($selectedOrder->status === 'UNPAID')
                <button wire:click="markAsPaid({{ $selectedOrder->id }})" wire:loading.attr="disabled"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center font-medium shadow-sm transition">
                    <span wire:loading.remove wire:target="markAsPaid">Mark as PAID</span>
                    <span wire:loading wire:target="markAsPaid">Processing...</span>
                </button>
                @endif
                <button wire:click="closeDetail"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Create Order Modal -->
    @if($showCreateModal)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 transition-opacity"
        wire:click.self="closeCreateModal">
        <div
            class="relative top-20 mx-auto p-0 border w-full max-w-lg shadow-xl rounded-xl bg-white dark:bg-gray-800 overflow-hidden transform transition-all">
            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-600 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Tambah Order Baru
                </h3>
                <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form wire:submit="storeOrder" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Event *</label>
                        <select wire:model="newEventId"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                            <option value="">-- Pilih Event --</option>
                            @foreach($activeEvents as $event)
                            <option value="{{ $event->id }}">{{ $event->name }} (Rp {{ number_format($event->price, 0,
                                ',', '.') }})</option>
                            @endforeach
                        </select>
                        @error('newEventId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Customer
                            *</label>
                        <input type="text" wire:model="newCustomerName"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                            placeholder="John Doe">
                        @error('newCustomerName') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">WhatsApp Phone
                            *</label>
                        <input type="text" wire:model="newPhone"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                            placeholder="628123456789 (Awalan 62)">
                        <p class="text-xs text-gray-500 mt-1">Format: 628xxx (Tanpa 0 di depan, tanpa +)</p>
                        @error('newPhone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Qty Tiket
                            *</label>
                        <input type="number" wire:model="newQty" min="1" max="100"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        @error('newQty') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" wire:click="closeCreateModal"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-sm transition">
                        Simpan Order
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Detail Modal -->
    @if($showDetail && $selectedOrder)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 transition-opacity"
        wire:click.self="closeDetail">
        <div
            class="relative top-10 mx-auto p-0 border w-full max-w-2xl shadow-xl rounded-xl bg-white dark:bg-gray-800 overflow-hidden transform transition-all">
            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-600 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Order #{{ $selectedOrder->id }}
                </h3>
                <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <!-- Order Info Grid -->
                <div class="grid grid-cols-2 gap-x-6 gap-y-4 mb-6">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Event</label>
                        <p class="font-bold text-gray-900 dark:text-white">{{ $selectedOrder->event->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
                        <p class="mt-1">
                            @if($selectedOrder->status === 'UNPAID')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">UNPAID</span>
                            @elseif($selectedOrder->status === 'PAID')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">PAID</span>
                            @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">CANCELLED</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Customer</label>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $selectedOrder->customer_name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Phone</label>
                        <p class="font-medium text-gray-900 dark:text-white font-mono">{{ $selectedOrder->phone }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Qty</label>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $selectedOrder->qty }} Tiket</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Created</label>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $selectedOrder->created_at->format('d M
                            Y H:i') }}</p>
                    </div>
                </div>

                <!-- Tickets Section -->
                @if($selectedOrder->tickets->count() > 0)
                <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                    <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                            </path>
                        </svg>
                        Generated Tickets ({{ $selectedOrder->tickets->count() }})
                    </h4>
                    <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                        @foreach($selectedOrder->tickets as $ticket)
                        <div
                            class="flex justify-between items-center p-2.5 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                            <span class="text-sm font-mono font-medium text-gray-700 dark:text-white">{{ $ticket->code
                                }}</span>
                            <span class="px-2 py-1 text-xs font-bold rounded 
                                            @if($ticket->status === 'ISSUED') bg-blue-50 text-blue-700 border border-blue-100
                                            @elseif($ticket->status === 'CHECKED_IN') bg-green-50 text-green-700 border border-green-100
                                            @else bg-red-50 text-red-700 border border-red-100 @endif">
                                {{ $ticket->status }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-100 dark:border-gray-600 flex justify-end space-x-3">
                @if($selectedOrder->status === 'UNPAID')
                <button wire:click="markAsPaid({{ $selectedOrder->id }})" wire:loading.attr="disabled"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center font-medium shadow-sm transition">
                    <span wire:loading.remove wire:target="markAsPaid">Mark as PAID</span>
                    <span wire:loading wire:target="markAsPaid">Processing...</span>
                </button>
                @endif
                <button wire:click="closeDetail"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif
</div>