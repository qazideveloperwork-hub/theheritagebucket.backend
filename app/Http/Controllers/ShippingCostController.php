<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingCostController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('shippingCost')
            ->where('status', '!=', 'deleted')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q');
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('settings.shipping', compact('products'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'shipping_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $product->shippingCost()->updateOrCreate([], $data);

        return redirect()->route('settings.shipping')->with('success', "Shipping cost for \"{$product->name}\" was updated.");
    }
}
