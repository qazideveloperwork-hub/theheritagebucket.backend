<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Slider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Shared logic for the "pick products into an ordered list" slider screens
// (New Arrival, Favourite Product) — identical shape, only the `type()` differs.
abstract class ProductSliderController extends Controller
{
    abstract protected function type(): string;

    abstract protected function viewName(): string;

    abstract protected function indexRoute(): string;

    public function index(): View
    {
        $slides = Slider::ofType($this->type())->with('product')->orderBy('sort_order')->get();

        $availableProducts = Product::where('status', '!=', 'deleted')
            ->whereNotIn('id', $slides->pluck('product_id'))
            ->orderBy('name')
            ->get();

        return view($this->viewName(), compact('slides', 'availableProducts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $nextOrder = Slider::ofType($this->type())->max('sort_order') + 1;

        Slider::create([
            'type' => $this->type(),
            'product_id' => $data['product_id'],
            'sort_order' => $nextOrder,
        ]);

        return redirect()->route($this->indexRoute())->with('success', 'Product added.');
    }

    public function destroy(Slider $slider): RedirectResponse
    {
        abort_unless($slider->type === $this->type(), 404);

        $slider->delete();

        return redirect()->route($this->indexRoute())->with('success', 'Product removed.');
    }
}
