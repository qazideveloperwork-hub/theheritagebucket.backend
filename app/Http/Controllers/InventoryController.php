<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['inventory', 'variations.size', 'variations.inventory'])
            ->where('status', '!=', 'deleted')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q');
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%")
                        ->orWhereHas('variations', fn ($query) => $query->where('sku', 'like', "%{$term}%"));
                });
            })
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return view('purchase.stock', compact('products'));
    }

    public function update(Request $request, Inventory $inventory): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $inventory->update(['quantity' => $data['quantity']]);

        $owner = $inventory->product ?: $inventory->productVariation;

        if ($owner && ! $owner->stock_status_manual) {
            $owner->update(['stock_status' => $data['quantity'] > 0 ? 'in_stock' : 'out_of_stock']);
        }

        return redirect()->route('purchase.stock')->with('success', 'Stock quantity updated.');
    }
}
