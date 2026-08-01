<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingPolicyController extends Controller
{
    private const SLUG = 'shipping-policy';

    public function edit(): View
    {
        $page = Page::firstOrCreate(['slug' => self::SLUG], ['title' => 'Shipping Policy']);

        return view('pages.shipping-policy', ['page' => $page]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ]);

        Page::firstOrCreate(['slug' => self::SLUG], ['title' => 'Shipping Policy'])->update($data);

        return redirect()->route('pages.shipping-policy')->with('success', 'Shipping policy was updated.');
    }
}
