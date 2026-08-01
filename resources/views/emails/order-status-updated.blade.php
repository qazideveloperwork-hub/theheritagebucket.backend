@php
    $copy = [
        'processing' => [
            'eyebrow' => 'Order Update',
            'heading' => 'Your order is being processed',
            'body' => 'Good news — we\'ve started preparing your order. We\'ll let you know as soon as it ships.',
        ],
        'delivered' => [
            'eyebrow' => 'Order Complete',
            'heading' => 'Your order has been completed',
            'body' => 'Your order has been delivered. We hope you love your new pieces — thank you for shopping with Heritage Basket.',
        ],
        'cancelled' => [
            'eyebrow' => 'Order Cancelled',
            'heading' => 'Your order has been cancelled',
            'body' => 'Your order has been cancelled. If you didn\'t request this or have any questions, please get in touch with us.',
        ],
    ][$status] ?? [
        'eyebrow' => 'Order Update',
        'heading' => 'Your order status has been updated',
        'body' => 'Your order status is now: '.ucfirst($status).'.',
    ];
@endphp
@component('emails.layout', ['subject' => $copy['heading']])
    <p style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#a5360d; margin:0 0 8px;">{{ $copy['eyebrow'] }}</p>
    <h1 style="font-family: Georgia, serif; font-size:24px; color:#1b1b18; margin:0 0 16px;">{{ $copy['heading'] }}</h1>
    <p style="margin:0 0 4px;">Hi {{ $order->customer_name }},</p>
    <p style="margin:0 0 24px;">{{ $copy['body'] }}</p>
    <p style="margin:0 0 24px;">Order Number: <strong style="color:#a5360d;">{{ $order->order_number }}</strong></p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#faf4ec; border:1px solid #e6d9c5;">
        <tr>
            <td style="padding:20px;">
                <p style="font-family: Georgia, serif; font-size:16px; color:#1b1b18; margin:0 0 12px;">Order Summary</p>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;">
                    @foreach ($order->items as $item)
                        <tr>
                            <td style="padding:4px 0; color:#5a4538;">{{ $item->product_name }}{{ $item->variation_label ? ' ('.$item->variation_label.')' : '' }} &times; {{ $item->quantity }}</td>
                            <td style="padding:4px 0; color:#1b1b18; text-align:right;">Rs. {{ number_format((float) $item->line_total) }}</td>
                        </tr>
                    @endforeach
                </table>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px; border-top:1px solid #e6d9c5; margin-top:12px;">
                    <tr>
                        <td style="padding-top:8px; font-family: Georgia, serif; font-size:16px; color:#1b1b18;">Total</td>
                        <td style="padding-top:8px; font-family: Georgia, serif; font-size:16px; color:#1b1b18; text-align:right;">Rs. {{ number_format((float) $order->total) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @slot('showcase')
        @include('emails.partials.product-showcase')
    @endslot
@endcomponent
