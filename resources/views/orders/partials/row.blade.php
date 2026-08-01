@php
    $statusStyles = [
        'pending' => 'bg-[#f1e5d5] text-[#8a6c56]',
        'processing' => 'bg-[#fdf1e6] text-[#7c2d12]',
        'shipped' => 'bg-[#e6eef6] text-[#2c4a6a]',
        'delivered' => 'bg-[#eaf3e5] text-[#3f6a2c]',
        'cancelled' => 'bg-[#fdecea] text-[#b91c1c]',
    ];
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
    <td class="px-5 py-3">
        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusStyles[$order->status] ?? 'bg-[#f1e5d5] text-[#8a6c56]' }}">
            {{ ucfirst($order->status) }}
        </span>
    </td>
    <td class="px-5 py-3 text-[#5a4538]">{{ $order->created_at->format('d M Y') }}</td>
    <td class="px-5 py-3 text-[#5a4538]">{{ $order->shippingDate()->format('d M Y') }}</td>
    <td class="px-5 py-3 text-right">
        <a href="{{ route('orders.edit', $order) }}" class="rounded-full border border-[#d9c9ae] px-4 py-1.5 text-xs font-medium text-[#5a4538] hover:bg-[#f1e5d5]">Edit</a>
    </td>
</tr>
