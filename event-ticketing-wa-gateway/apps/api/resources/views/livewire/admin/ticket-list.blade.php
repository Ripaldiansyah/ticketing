<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="mb-6 flex flex-wrap gap-4 items-center">
            <div class="relative w-full md:w-1/3">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kode/nama/phone..."
                    class="pl-10 w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white shadow-sm">
            </div>

            <select wire:model.live="statusFilter"
                class="px-4 py-2 border border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white shadow-sm cursor-pointer">
                <option value="">Semua Status</option>
                <option value="ISSUED">ISSUED</option>
                <option value="CHECKED_IN">CHECKED_IN</option>
                <option value="REJECTED">REJECTED</option>
            </select>

            <select wire:model.live="eventFilter"
                class="px-4 py-2 border border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white shadow-sm cursor-pointer w-48">
                <option value="">Semua Event</option>
                @foreach($events as $event)
                <option value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Table -->
        <div
            class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Kode</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Event</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Customer</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm font-mono font-medium text-gray-900 dark:text-white">
                            {{ $ticket->code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $ticket->event->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $ticket->order->customer_name }}
                            </div>
                            <div class="text-xs">{{ $ticket->order->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($ticket->status === 'ISSUED')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <span class="w-1.5 h-1.5 mr-1.5 bg-blue-400 rounded-full"></span>
                                ISSUED
                            </span>
                            @elseif($ticket->status === 'CHECKED_IN')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="w-1.5 h-1.5 mr-1.5 bg-green-400 rounded-full"></span>
                                CHECKED_IN
                            </span>
                            @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <span class="w-1.5 h-1.5 mr-1.5 bg-red-400 rounded-full"></span>
                                REJECTED
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="showTicketDetail({{ $ticket->id }})"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 font-medium">Detail</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                    </path>
                                </svg>
                                <p>Tidak ada tiket</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    </div>

    <!-- Detail Modal -->
    @if($showDetail && $selectedTicket)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 transition-opacity"
        wire:click.self="closeDetail">
        <div
            class="relative top-10 mx-auto p-0 border w-full max-w-3xl shadow-xl rounded-xl bg-white dark:bg-gray-800 overflow-hidden transform transition-all">
            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-600 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Tiket #<span class="font-mono">{{ $selectedTicket->code }}</span>
                </h3>
                <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column - Info -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Event
                            Details</label>
                        <h4 class="font-bold text-gray-900 dark:text-white">{{ $selectedTicket->event->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $selectedTicket->event->date_start->format('d M Y, H:i') }}
                            WIB</p>
                        <p class="text-sm text-gray-500">{{ $selectedTicket->event->venue ?? 'No Venue' }}</p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                        <label
                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Customer</label>
                        <div class="flex items-center">
                            <div
                                class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold mr-3">
                                {{ substr($selectedTicket->order->customer_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{
                                    $selectedTicket->order->customer_name }}</p>
                                <p class="text-sm text-gray-500">{{ $selectedTicket->order->phone }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">QR
                            Code</label>
                        @if($selectedTicket->qr_path)
                        <div class="flex items-center space-x-4">
                            <img src="{{ asset('storage/' . $selectedTicket->qr_path) }}" alt="QR Code"
                                class="w-24 h-24 border rounded-lg p-1">
                            <div>
                                <a href="{{ asset('storage/' . $selectedTicket->qr_path) }}" download
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                                    Download
                                </a>
                            </div>
                        </div>
                        @else
                        <span class="text-sm text-gray-400">QR Code not generated</span>
                        @endif
                    </div>
                </div>

                <!-- Right Column - Audit History -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                    <h4 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Timeline / History
                    </h4>
                    <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2">
                        <!-- Current Status -->
                        <div class="relative pl-4 border-l-2 border-indigo-200">
                            <div class="absolute -left-[5px] top-1 h-2 w-2 rounded-full bg-indigo-500"></div>
                            <p class="text-xs text-gray-500 mb-0.5">Current Status</p>
                            <span class="px-2 py-0.5 rounded text-xs font-bold 
                                @if($selectedTicket->status === 'ISSUED') bg-blue-100 text-blue-800
                                @elseif($selectedTicket->status === 'CHECKED_IN') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $selectedTicket->status }}
                            </span>
                        </div>

                        @forelse($selectedTicket->audits as $audit)
                        <div class="relative pl-4 border-l-2 border-gray-200 dark:border-gray-600">
                            <div class="absolute -left-[5px] top-1 h-2 w-2 rounded-full bg-gray-400"></div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $audit->action
                                    }}</span>
                                <span class="text-xs text-gray-500">{{ $audit->created_at->format('d M Y H:i') }}</span>
                                <span class="text-xs text-gray-500">by {{ $audit->user->name }}</span>

                                @if($audit->action === 'REJECT' && !empty($audit->payload['reason']))
                                <div
                                    class="mt-1 p-2 bg-red-50 dark:bg-red-900/20 rounded border border-red-100 dark:border-red-900/30">
                                    <p class="text-xs text-red-600 dark:text-red-400 italic">"{{
                                        $audit->payload['reason'] }}"</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        @endforelse

                        <div class="relative pl-4 border-l-2 border-gray-200 dark:border-gray-600">
                            <div class="absolute -left-[5px] top-1 h-2 w-2 rounded-full bg-gray-400"></div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">CREATED</span>
                                <span class="text-xs text-gray-500">{{ $selectedTicket->created_at->format('d M Y H:i')
                                    }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-100 dark:border-gray-600 flex justify-end">
                <button wire:click="closeDetail"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium shadow-sm transition">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif
</div>