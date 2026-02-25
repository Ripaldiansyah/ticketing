<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        // 1. Total Revenue (PAID orders)
        // Assuming default price is calculated? Or we just count orders?
        // Since we don't have 'price' in Order model explicit (it was price * qty), 
        // let's assume we count 'Qty' sold and 'Count' of orders for now if price is missing.
        // Wait, Event has 'price'.

        $totalRevenue = Order::where('status', 'PAID')
            ->join('events', 'orders.event_id', '=', 'events.id')
            ->sum(DB::raw('orders.qty * events.price'));

        // 2. Ticket Sales
        $totalTicketsSold = Ticket::count();
        $totalCheckIn = Ticket::where('status', 'CHECKED_IN')->count();

        // 3. Orders Stats
        $pendingOrders = Order::where('status', 'UNPAID')->count();
        $successOrders = Order::where('status', 'PAID')->count();

        // 4. Upcoming Events
        $upcomingEvents = Event::where('date_start', '>=', now())
            ->orderBy('date_start')
            ->take(3)
            ->get();

        // 5. Recent Orders
        $recentOrders = Order::with('event')
            ->latest()
            ->take(5)
            ->get();

        // 6. Monthly Sales Chart Data (Simple text based or array for now)
        // Group by Date for last 7 days
        $salesChart = Order::where('status', 'PAID')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        return view('livewire.dashboard', compact(
            'totalRevenue',
            'totalTicketsSold',
            'totalCheckIn',
            'pendingOrders',
            'successOrders',
            'upcomingEvents',
            'recentOrders',
            'salesChart'
        ))->layout('layouts.app');
    }
}
