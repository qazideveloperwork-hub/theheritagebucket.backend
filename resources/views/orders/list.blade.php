@extends('layout')
@section('title', 'Orders - List')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Orders'],
    ]])

    <h1 class="text-2xl font-semibold text-[#523923] mb-6">Order List</h1>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-[#c7d9c0] bg-[#eef6ea] px-4 py-3 text-sm text-[#3f6a2c]">{{ session('success') }}</div>
    @endif

    <form action="{{ route('orders.list') }}" method="GET" class="mb-6 flex flex-wrap items-center gap-3 rounded-[28px] border border-[#e6d9c5] bg-white px-5 py-4 text-sm">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search order #, customer, email" class="min-w-[220px] flex-1 rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-2.5 text-sm outline-none focus:border-[#7c2d12]" />
        <select name="status" class="rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-2.5 text-sm outline-none focus:border-[#7c2d12]">
            <option value="">All statuses</option>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-full bg-[#7C2D12] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#6b2610]">Filter</button>
        @if (request('q') || request('status'))
            <a href="{{ route('orders.list') }}" class="text-sm text-[#8a6c56] hover:underline">Clear</a>
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
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Placed</th>
                    <th class="px-5 py-3">Shipping Date</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f3e6d8]">
                @forelse ($orders as $order)
                    @include('orders.partials.row', ['order' => $order])
                @empty
                    <tr>
                        <td colspan="9" class="px-5 py-10 text-center text-sm text-[#8a6c56]">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
@endsection
