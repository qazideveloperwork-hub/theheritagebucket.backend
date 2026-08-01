<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ColorController extends Controller
{
    public function index(): View
    {
        $colors = Color::orderBy('name')->paginate(20);

        return view('color.list', compact('colors'));
    }

    public function create(): View
    {
        return view('color.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'hex_code' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        Color::create($data);

        return redirect()->route('colors.list')->with('success', "Color \"{$data['name']}\" was created.");
    }

    public function edit(Color $color): View
    {
        return view('color.edit', compact('color'));
    }

    public function update(Request $request, Color $color): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'hex_code' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $color->update($data);

        return redirect()->route('colors.list')->with('success', "Color \"{$color->name}\" was updated.");
    }

    public function destroy(Color $color): RedirectResponse
    {
        $name = $color->name;
        $color->delete();

        return redirect()->route('colors.list')->with('success', "Color \"{$name}\" was deleted.");
    }
}
