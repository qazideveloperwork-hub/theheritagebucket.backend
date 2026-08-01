@extends('layout')
@section('title', 'User: '.$customer->name)
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Users', 'url' => route('users.index')],
        ['label' => $customer->name],
    ]])

    @include('partials.new-password-banner')

    @php
        $statusStyles = [
            'active' => 'bg-[#eaf3e5] text-[#3f6a2c]',
            'blocked' => 'bg-[#f1e5d5] text-[#8a6c56]',
            'deleted' => 'bg-[#fdecea] text-[#b91c1c]',
        ];
    @endphp

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-semibold text-[#523923]">{{ $customer->name }}</h1>
        <div class="flex gap-3">
            <form action="{{ route('users.reset-password', $customer) }}" method="POST" data-confirm="Reset password for {{ addslashes($customer->name) }}? Their current password will stop working.">
                @csrf
                <button type="submit" class="rounded-full border border-[#d9c9ae] px-5 py-2.5 text-sm font-medium text-[#5a4538] hover:bg-[#f1e5d5]">Reset Password</button>
            </form>
            <form action="{{ route('users.status', $customer) }}" method="POST" data-confirm="{{ $customer->status === 'blocked' ? 'Activate' : 'Block' }} {{ addslashes($customer->name) }}?">
                @csrf
                @method('PATCH')
                <button type="submit" class="rounded-full border border-[#d9c9ae] px-5 py-2.5 text-sm font-medium text-[#5a4538] hover:bg-[#f1e5d5]">
                    {{ $customer->status === 'blocked' ? 'Activate' : 'Block' }}
                </button>
            </form>
            <form action="{{ route('users.destroy', $customer) }}" method="POST" data-confirm="Delete {{ addslashes($customer->name) }}? They will be marked as Deleted and hidden from this list.">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-full border border-[#e3b3a3] px-5 py-2.5 text-sm font-medium text-[#7c2d12] hover:bg-[#fdecea]">Delete</button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
                <h2 class="text-lg font-semibold text-[#523923]">Profile</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 text-sm">
                    <div><span class="block text-xs uppercase text-[#8a6c56]">Email</span>{{ $customer->email }}</div>
                    <div><span class="block text-xs uppercase text-[#8a6c56]">Phone</span>{{ $customer->phone ?: '—' }}</div>
                    <div><span class="block text-xs uppercase text-[#8a6c56]">Joined</span>{{ $customer->created_at->format('d M Y, H:i') }}</div>
                    <div>
                        <span class="block text-xs uppercase text-[#8a6c56]">Status</span>
                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $customer->status === 'blocked' ? 'bg-[#fdecea] text-[#b91c1c]' : 'bg-[#eaf3e5] text-[#3f6a2c]' }}">
                            {{ ucfirst($customer->status) }}
                        </span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="block text-xs uppercase text-[#8a6c56]">Address</span>
                        {{ collect([$customer->street, $customer->address_line_2, $customer->city, $customer->province, $customer->postal_code])->filter()->join(', ') ?: '—' }}
                    </div>
                </div>
            </section>

            <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
                <h2 class="text-lg font-semibold text-[#523923]">Orders</h2>
                <table class="mt-4 w-full text-left text-sm">
                    <thead class="text-xs uppercase tracking-wide text-[#8a6c56]">
                        <tr>
                            <th class="py-2">Order #</th>
                            <th class="py-2">Total</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Placed</th>
                            <th class="py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f3e6d8]">
                        @forelse ($customer->orders as $order)
                            <tr>
                                <td class="py-2 font-medium text-[#1b1b18]">{{ $order->order_number }}</td>
                                <td class="py-2">Rs. {{ number_format((float) $order->total) }}</td>
                                <td class="py-2">{{ ucfirst($order->status) }}</td>
                                <td class="py-2">{{ $order->created_at->format('d M Y') }}</td>
                                <td class="py-2 text-right">
                                    <a href="{{ route('orders.edit', $order) }}" class="rounded-full border border-[#d9c9ae] px-4 py-1.5 text-xs font-medium text-[#5a4538] hover:bg-[#f1e5d5]">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-[#8a6c56]">No orders yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        </div>
    </div>
@endsection
