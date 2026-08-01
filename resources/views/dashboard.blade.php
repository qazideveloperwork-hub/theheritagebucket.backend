@extends('layout')

@section('title', 'Admin Dashboard')

@section('content')
    @php
        $statusStyles = [
            'pending' => 'bg-[#f1e5d5] text-[#8a6c56]',
            'processing' => 'bg-[#fdf1e6] text-[#7c2d12]',
            'shipped' => 'bg-[#e6eef6] text-[#2c4a6a]',
            'delivered' => 'bg-[#eaf3e5] text-[#3f6a2c]',
            'cancelled' => 'bg-[#fdecea] text-[#b91c1c]',
        ];
    @endphp
    <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
        <section class="rounded-[32px] border border-[#dbc9b2] bg-white/90 p-8 shadow-[0_20px_55px_-40px_rgba(0,0,0,0.13)]">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.35em] text-[#8a6c56]">Admin dashboard</p>
                    <h1 class="mt-3 text-3xl font-semibold text-[#523923]">Hello, admin.</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-[#5a4538]">Manage orders, inventory, content, and system policies from a single place.</p>
                </div>
                <div class="rounded-3xl bg-[#f7ece0] p-4 text-[#7c2d12] shadow-sm ring-1 ring-[#7c2d12]/10">
                    <p class="text-xs uppercase tracking-[0.35em] text-[#7c2d12]/90">Live status</p>
                    <p class="mt-3 text-4xl font-semibold">Good</p>
                </div>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <article class="rounded-[28px] border border-[#f3e6d8] bg-[#fff7ef] p-6">
                    <p class="text-sm uppercase tracking-[0.35em] text-[#8b6a54]">Orders today</p>
                    <p class="mt-4 text-3xl font-semibold text-[#7c2d12]">{{ number_format($ordersToday) }}</p>
                </article>
                <article class="rounded-[28px] border border-[#f3e6d8] bg-[#f8f4ee] p-6">
                    <p class="text-sm uppercase tracking-[0.35em] text-[#8b6a54]">Draft products</p>
                    <p class="mt-4 text-3xl font-semibold text-[#7c2d12]">{{ number_format($draftProducts) }}</p>
                </article>
                <article class="rounded-[28px] border border-[#f3e6d8] bg-[#fff7ef] p-6">
                    <p class="text-sm uppercase tracking-[0.35em] text-[#8b6a54]">Subscribers</p>
                    <p class="mt-4 text-3xl font-semibold text-[#7c2d12]">{{ number_format($subscribers) }}</p>
                </article>
                <article class="rounded-[28px] border border-[#f3e6d8] bg-[#f8f4ee] p-6">
                    <p class="text-sm uppercase tracking-[0.35em] text-[#8b6a54]">Pending orders</p>
                    <p class="mt-4 text-3xl font-semibold text-[#7c2d12]">{{ number_format($pendingOrders) }}</p>
                </article>
            </div>

            <div class="mt-4 rounded-[28px] border border-[#f3e6d8] bg-[#fffaf4] p-6">
                <p class="text-sm uppercase tracking-[0.35em] text-[#8b6a54]">Orders, last 14 days</p>
                <div class="mt-4" style="height: 260px">
                    <canvas id="orders-chart"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-[32px] border border-[#dbc9b2] bg-white/90 p-8 shadow-[0_20px_55px_-40px_rgba(0,0,0,0.13)]">
            <h2 class="text-xl font-semibold text-[#523923]">Recent activity</h2>
            <div class="mt-6 space-y-4">
                @forelse ($recentOrders as $index => $order)
                    <a href="{{ route('orders.edit', $order) }}" class="block rounded-3xl border border-[#f3e6d8] {{ $index % 2 === 0 ? 'bg-[#fff7ef]' : 'bg-[#f8f4ee]' }} p-4 hover:border-[#d9c9ae]">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-semibold text-[#603b22]">{{ $order->order_number }} &middot; {{ $order->customer_name }}</p>
                            <span class="shrink-0 rounded-full px-3 py-1 text-xs font-medium {{ $statusStyles[$order->status] ?? 'bg-[#f1e5d5] text-[#8a6c56]' }}">{{ ucfirst($order->status) }}</span>
                        </div>
                        <p class="mt-1 text-sm text-[#5a4538]">Rs. {{ number_format((float) $order->total) }} &middot; placed {{ $order->created_at->diffForHumans() }}</p>
                    </a>
                @empty
                    <p class="text-sm text-[#8a6c56]">No orders yet.</p>
                @endforelse
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        new Chart(document.getElementById('orders-chart'), {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Orders',
                    data: @json($chartData),
                    borderColor: '#7c2d12',
                    backgroundColor: 'rgba(124, 45, 18, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHitRadius: 20,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        align: 'top',
                        anchor: 'end',
                        offset: 6,
                        color: '#7c2d12',
                        font: { weight: 'bold', size: 12 },
                        formatter: (value) => value > 0 ? value : '',
                    },
                },
                layout: { padding: { top: 20 } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                },
            },
            plugins: [ChartDataLabels],
        });
    </script>
@endsection
