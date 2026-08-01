@extends('layout')
@section('title', 'Edit Order')
@section('content')
    @php
        $paymentLabels = [
            'cod' => 'Cash on Delivery',
            'jazzcash' => 'JazzCash',
            'bank_transfer' => 'Bank Transfer',
        ];
    @endphp
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Orders', 'url' => route('orders.list')],
        ['label' => $order->order_number],
    ]])

    <h1 class="text-2xl font-semibold text-[#523923] mb-6">Order {{ $order->order_number }}</h1>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-[#c7d9c0] bg-[#eef6ea] px-4 py-3 text-sm text-[#3f6a2c]">{{ session('success') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
                <h2 class="text-lg font-semibold text-[#523923]">Customer &amp; Shipping</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 text-sm">
                    <div><span class="block text-xs uppercase text-[#8a6c56]">Name</span>{{ $order->customer_name }}</div>
                    <div><span class="block text-xs uppercase text-[#8a6c56]">Email</span>{{ $order->email }}</div>
                    <div><span class="block text-xs uppercase text-[#8a6c56]">Phone</span>{{ $order->phone }}</div>
                    <div><span class="block text-xs uppercase text-[#8a6c56]">Placed on</span>{{ $order->created_at->format('d M Y, H:i') }}</div>
                    <div><span class="block text-xs uppercase text-[#8a6c56]">Shipping Date</span>{{ $order->shippingDate()->format('d M Y') }}</div>
                    <div class="sm:col-span-2"><span class="block text-xs uppercase text-[#8a6c56]">Address</span>{{ $order->shipping_address }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</div>
                    @if ($order->notes)
                        <div class="sm:col-span-2"><span class="block text-xs uppercase text-[#8a6c56]">Customer notes</span>{{ $order->notes }}</div>
                    @endif
                </div>
            </section>

            <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
                <h2 class="text-lg font-semibold text-[#523923]">Items</h2>
                <table class="mt-4 w-full text-left text-sm">
                    <thead class="text-xs uppercase tracking-wide text-[#8a6c56]">
                        <tr>
                            <th class="py-2">Product</th>
                            <th class="py-2">Unit price</th>
                            <th class="py-2">Qty</th>
                            <th class="py-2 text-right">Line total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f3e6d8]">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="py-2">{{ $item->product_name }}{{ $item->variation_label ? ' ('.$item->variation_label.')' : '' }}</td>
                                <td class="py-2">Rs. {{ number_format((float) $item->unit_price) }}</td>
                                <td class="py-2">{{ $item->quantity }}</td>
                                <td class="py-2 text-right">Rs. {{ number_format((float) $item->line_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4 space-y-1 border-t border-[#f3e6d8] pt-4 text-sm text-[#5a4538]">
                    <div class="flex justify-between"><span>Subtotal</span><span>Rs. {{ number_format((float) $order->subtotal) }}</span></div>
                    <div class="flex justify-between"><span>Shipping cost</span><span>Rs. {{ number_format((float) $order->shipping_fee) }}</span></div>
                    <div class="flex justify-between font-semibold text-[#1b1b18]"><span>Total</span><span>Rs. {{ number_format((float) $order->total) }}</span></div>
                </div>
            </section>
        </div>

        <div class="space-y-6">
            <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6 text-sm">
                <h2 class="text-lg font-semibold text-[#523923]">Payment</h2>
                <p class="mt-3 text-[#5a4538]">{{ $paymentLabels[$order->payment_method] ?? ucfirst($order->payment_method) }}</p>
                @if ($order->isDelayed())
                    <p class="mt-3 rounded-full bg-[#fdecea] px-3 py-1 text-xs font-medium text-[#b91c1c] inline-block">Delayed</p>
                @endif
            </section>

            <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
                <h2 class="text-lg font-semibold text-[#523923]">Fulfilment</h2>
                <form action="{{ route('orders.update', $order) }}" method="POST" class="mt-4 space-y-4"
                      onsubmit="if (this.status.value === '{{ $order->status }}' || this.dataset.confirmed) { return true; } event.preventDefault(); openConfirmModalForForm(this, 'Are you sure you want to change the status to \'' + this.status.options[this.status.selectedIndex].text + '\'?'); return false;">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#5a4538]">Status</label>
                        <select name="status" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#5a4538]">Internal notes</label>
                        <textarea name="notes" rows="4" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">{{ old('notes', $order->notes) }}</textarea>
                    </div>
                    <button type="submit" class="w-full rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save</button>
                </form>
            </section>
        </div>
    </div>
@endsection
