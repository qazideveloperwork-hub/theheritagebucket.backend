<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedEmail extends Mailable
{
    use Queueable, SerializesModels;

    private const SUBJECTS = [
        'processing' => 'Your order is being processed',
        'delivered' => 'Your order has been completed',
        'cancelled' => 'Your order has been cancelled',
    ];

    public function __construct(
        public readonly Order $order,
        public readonly string $status,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (self::SUBJECTS[$this->status] ?? 'Your order status has been updated').' — '.$this->order->order_number,
            bcc: array_filter([Setting::current()->contact_email]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status-updated',
            with: [
                'order' => $this->order->load('items'),
                'status' => $this->status,
            ],
        );
    }
}
