@extends('layout')
@section('title', 'Orders - Completed')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Orders', 'url' => route('orders.list')],
        ['label' => 'Completed'],
    ]])

    <h1 class="text-2xl font-semibold text-[#523923] mb-6">Completed Orders</h1>

    <form action="{{ route('orders.completed') }}" method="GET" class="mb-6 flex flex-wrap items-end gap-3 rounded-[28px] border border-[#e6d9c5] bg-white px-5 py-4 text-sm">
        <div>
            <label class="mb-1 block text-xs font-medium text-[#5a4538]">From</label>
            <input type="date" name="from" value="{{ $from }}" class="rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-2.5 text-sm outline-none focus:border-[#7c2d12]" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-[#5a4538]">To</label>
            <input type="date" name="to" value="{{ $to }}" class="rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-2.5 text-sm outline-none focus:border-[#7c2d12]" />
        </div>
        <button type="submit" class="rounded-full bg-[#7C2D12] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#6b2610]">Filter</button>
        @if ($from || $to)
            <a href="{{ route('orders.completed') }}" class="pb-2.5 text-sm text-[#8a6c56] hover:underline">Clear</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-[28px] border border-[#e6d9c5] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#fff7ef] text-xs uppercase tracking-wide text-[#8a6c56]">
                <tr>
                    <th class="px-5 py-3">Order #</th>
                    <th class="px-5 py-3">Customer</th>
                    <th class="px-5 py-3">City</th>
                    <th class="px-5 py-3">Total</th>
                    <th class="px-5 py-3">Payment</th>
                    <th class="px-5 py-3">Placed</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f3e6d8]">
                @forelse ($orders as $order)
                    @php
                        $paymentLabels = [
                            'cod' => 'Cash on Delivery',
                            'jazzcash' => 'JazzCash',
                            'bank_transfer' => 'Bank Transfer',
                        ];
                    @endphp
                    <tr>
                        <td class="px-5 py-3 font-medium text-[#1b1b18]">{{ $order->order_number }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">
                            {{ $order->customer_name }}
                            <span class="block text-xs text-[#8a6c56]">{{ $order->email }}</span>
                        </td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $order->city }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">Rs. {{ number_format((float) $order->total) }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-[#f1e5d5] px-3 py-1 text-xs font-medium text-[#8a6c56]">
                                {{ $paymentLabels[$order->payment_method] ?? ucfirst($order->payment_method) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('orders.edit', $order) }}" class="rounded-full border border-[#d9c9ae] px-4 py-1.5 text-xs font-medium text-[#5a4538] hover:bg-[#f1e5d5]">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-[#8a6c56]">No completed orders in this range.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
@endsection
