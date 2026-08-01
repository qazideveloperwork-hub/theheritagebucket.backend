@extends('layout')
@section('title', 'Orders - Delayed')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Orders', 'url' => route('orders.list')],
        ['label' => 'Delayed'],
    ]])

    <h1 class="text-2xl font-semibold text-[#523923] mb-2">Delayed Orders</h1>
    <p class="mb-6 text-sm text-[#8a6c56]">Pending, processing, or shipped orders that are past their shipping date (order date + longest product lead time).</p>

    <div class="overflow-hidden rounded-[28px] border border-[#e6d9c5] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#fff7ef] text-xs uppercase tracking-wide text-[#8a6c56]">
                <tr>
                    <th class="px-5 py-3">Order #</th>
                    <th class="px-5 py-3">Customer</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Placed</th>
                    <th class="px-5 py-3">Shipping Date</th>
                    <th class="px-5 py-3">Overdue by</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f3e6d8]">
                @forelse ($orders as $order)
                    @php
                        $statusStyles = [
                            'pending' => 'bg-[#f1e5d5] text-[#8a6c56]',
                            'processing' => 'bg-[#fdf1e6] text-[#7c2d12]',
                            'shipped' => 'bg-[#e6eef6] text-[#2c4a6a]',
                        ];
                        $expected = $order->shippingDate();
                    @endphp
                    <tr>
                        <td class="px-5 py-3 font-medium text-[#1b1b18]">{{ $order->order_number }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">
                            {{ $order->customer_name }}
                            <span class="block text-xs text-[#8a6c56]">{{ $order->email }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusStyles[$order->status] ?? 'bg-[#f1e5d5] text-[#8a6c56]' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $expected->format('d M Y') }}</td>
                        <td class="px-5 py-3 font-medium text-[#b91c1c]">{{ max(1, (int) floor(now()->diffInDays($expected, true))) }} day(s)</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('orders.edit', $order) }}" class="rounded-full border border-[#d9c9ae] px-4 py-1.5 text-xs font-medium text-[#5a4538] hover:bg-[#f1e5d5]">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-[#8a6c56]">No delayed orders right now.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
@endsection
