<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Revenue -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Revenue</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp {{
                            number_format($totalRevenue, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-2 bg-green-50 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-green-600">
                    <span>From PAID orders</span>
                </div>
            </div>

            <!-- Tickets Sold -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tickets Issued</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{
                            number_format($totalTicketsSold) }}</h3>
                    </div>
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-gray-500">
                    <span class="text-blue-600 font-medium mr-1">{{ $successOrders }}</span> Paid Orders
                </div>
            </div>

            <!-- Check-in -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Check-in</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{
                            number_format($totalCheckIn) }}</h3>
                    </div>
                    <div class="p-2 bg-purple-50 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 w-full bg-gray-200 rounded-full h-1.5 dark:bg-gray-700">
                    <div class="bg-purple-600 h-1.5 rounded-full"
                        style="width: {{ $totalTicketsSold > 0 ? ($totalCheckIn / $totalTicketsSold * 100) : 0 }}%">
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pending Orders</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{
                            number_format($pendingOrders) }}</h3>
                    </div>
                    <div class="p-2 bg-yellow-50 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-yellow-600 font-medium">
                    Needs Action
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Recent Orders -->
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Orders</h3>
                    <a href="{{ route('admin.orders') }}"
                        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Order ID</th>
                                <th class="px-6 py-3">Customer</th>
                                <th class="px-6 py-3">Event</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($recentOrders as $order)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                    <div class="font-medium">{{ $order->customer_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->phone }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $order->event->name }}</td>
                                <td class="px-6 py-4">
                                    @if($order->status === 'PAID')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">PAID</span>
                                    @elseif($order->status === 'UNPAID')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">UNPAID</span>
                                    @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">CANCELLED</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No recent orders</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Upcoming Events -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Upcoming Events</h3>
                </div>
                <div class="p-6 flex-1 flex flex-col space-y-6">
                    @forelse($upcomingEvents as $event)
                    <div class="flex space-x-4">
                        <div
                            class="flex-shrink-0 w-16 h-16 bg-indigo-100 rounded-lg flex flex-col items-center justify-center text-indigo-700">
                            <span class="text-xs font-bold uppercase">{{ $event->date_start->format('M') }}</span>
                            <span class="text-xl font-bold">{{ $event->date_start->format('d') }}</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 dark:text-white line-clamp-1">{{ $event->name }}</h4>
                            <p class="text-sm text-gray-500 mt-1">{{ $event->venue }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $event->date_start->format('H:i') }} WIB</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-4">No upcoming events.</div>
                    @endforelse
                </div>
                <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 rounded-b-xl">
                    <a href="{{ route('admin.events') }}"
                        class="block w-full text-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-indigo-600 bg-white hover:bg-gray-50 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500 cursor-pointer">
                        Manage Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>