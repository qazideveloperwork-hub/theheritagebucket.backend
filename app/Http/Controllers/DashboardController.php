<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $chartDays = collect(range(13, 0))->map(fn ($daysAgo) => today()->subDays($daysAgo));

        // Aliased as "orders_count" rather than "total" — Order::casts() defines a
        // 'total' => 'decimal:2' cast, which Eloquent would otherwise also apply to
        // this raw COUNT(*) column, turning e.g. 6 into "6.00".
        $ordersByDay = Order::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as orders_count')
            ->where('created_at', '>=', today()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->pluck('orders_count', 'day');

        return view('dashboard', [
            'ordersToday' => Order::whereDate('created_at', today())->count(),
            'draftProducts' => Product::where('status', 'draft')->count(),
            'subscribers' => NewsletterSubscriber::count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'chartLabels' => $chartDays->map(fn (Carbon $day) => $day->format('M j'))->all(),
            'chartData' => $chartDays->map(fn (Carbon $day) => (int) $ordersByDay->get($day->toDateString(), 0))->all(),
            'recentOrders' => Order::latest()->limit(5)->get(),
        ]);
    }
}
