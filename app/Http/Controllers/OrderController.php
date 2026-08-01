<?php

namespace App\Http\Controllers;

use App\Mail\OrderStatusUpdatedEmail;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

    private const EMAIL_NOTIFIED_STATUSES = ['processing', 'delivered', 'cancelled'];

    public function index(Request $request): View
    {
        $orders = Order::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q');
                $query->where(function ($query) use ($term) {
                    $query->where('order_number', 'like', "%{$term}%")
                        ->orWhere('customer_name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->with('items.product')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('orders.list', [
            'orders' => $orders,
            'statuses' => self::STATUSES,
        ]);
    }

    public function delayed(Request $request): View
    {
        // Expected delivery date depends on each item's product lead time, which can't be
        // expressed as a plain SQL WHERE — filter active orders in PHP, then re-query for
        // pagination. Fine at this admin tool's scale (see docs/product-management-changes.md
        // for other places that made the same trade-off).
        $delayedIds = Order::query()
            ->whereIn('status', Order::ACTIVE_STATUSES)
            ->with('items.product')
            ->get()
            ->filter(fn (Order $order) => $order->isDelayed())
            ->pluck('id');

        $orders = Order::query()
            ->whereIn('id', $delayedIds)
            ->with('items.product')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('orders.delayed', ['orders' => $orders]);
    }

    public function completed(Request $request): View
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $orders = Order::query()
            ->where('status', 'delivered')
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('orders.completed', [
            'orders' => $orders,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function edit(Order $order): View
    {
        $order->load('items.product');

        return view('orders.edit', [
            'order' => $order,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', self::STATUSES)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldStatus = $order->status;

        DB::transaction(function () use ($order, $data) {
            $wasCancelled = $order->status === 'cancelled';
            $becomesCancelled = $data['status'] === 'cancelled';

            $order->update($data);

            if ($becomesCancelled && ! $wasCancelled) {
                $order->load('items.product', 'items.productVariation');
                foreach ($order->items as $item) {
                    $this->adjustStock($item, $item->quantity);
                }
            } elseif ($wasCancelled && ! $becomesCancelled) {
                $order->load('items.product', 'items.productVariation');
                foreach ($order->items as $item) {
                    $this->adjustStock($item, -$item->quantity);
                }
            }
        });

        if ($oldStatus !== $order->status && in_array($order->status, self::EMAIL_NOTIFIED_STATUSES, true)) {
            Mail::to($order->email)->send(new OrderStatusUpdatedEmail($order, $order->status));
        }

        return redirect()->route('orders.list')->with('success', "Order \"{$order->order_number}\" was updated.");
    }

    /**
     * Mirrors InventoryController::update()'s stock_status sync: only
     * auto-flip stock_status when the admin hasn't manually pinned it.
     * Positive $delta restocks (order cancelled), negative decrements
     * (order un-cancelled back to an active status).
     */
    private function adjustStock(OrderItem $item, int $delta): void
    {
        $owner = $item->productVariation ?: $item->product;

        if (! $owner) {
            return;
        }

        $inventory = $owner->inventory;

        if (! $inventory) {
            return;
        }

        $newQuantity = max(0, $inventory->quantity + $delta);
        $inventory->update(['quantity' => $newQuantity]);

        if (! $owner->stock_status_manual) {
            $owner->update(['stock_status' => $newQuantity > 0 ? 'in_stock' : 'out_of_stock']);
        }
    }
}
