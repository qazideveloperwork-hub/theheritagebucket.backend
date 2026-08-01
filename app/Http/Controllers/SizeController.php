<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SizeController extends Controller
{
    public function index(): View
    {
        $sizes = Size::orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('size.list', compact('sizes'));
    }

    public function create(): View
    {
        return view('size.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        Size::create([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()->route('sizes.list')->with('success', "Size \"{$data['name']}\" was created.");
    }

    public function edit(Size $size): View
    {
        return view('size.edit', compact('size'));
    }

    public function update(Request $request, Size $size): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $size->update([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()->route('sizes.list')->with('success', "Size \"{$size->name}\" was updated.");
    }

    public function destroy(Size $size): RedirectResponse
    {
        $name = $size->name;

        try {
            $size->delete();
        } catch (QueryException) {
            return redirect()->route('sizes.list')->with('error', "Size \"{$name}\" is used by existing product variations and can't be deleted.");
        }

        return redirect()->route('sizes.list')->with('success', "Size \"{$name}\" was deleted.");
    }
}
