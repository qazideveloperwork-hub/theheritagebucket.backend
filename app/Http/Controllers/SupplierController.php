<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::orderBy('name')->paginate(20);

        return view('supplier.list', compact('suppliers'));
    }

    public function create(): View
    {
        return view('supplier.create', ['cities' => config('pakistan_cities')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'in:'.implode(',', $this->allCities())],
        ]);

        Supplier::create($data);

        return redirect()->route('suppliers.list')->with('success', "Supplier \"{$data['name']}\" was created.");
    }

    public function edit(Supplier $supplier): View
    {
        return view('supplier.edit', ['supplier' => $supplier, 'cities' => config('pakistan_cities')]);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'in:'.implode(',', $this->allCities())],
        ]);

        $supplier->update($data);

        return redirect()->route('suppliers.list')->with('success', "Supplier \"{$supplier->name}\" was updated.");
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $name = $supplier->name;
        $supplier->delete();

        return redirect()->route('suppliers.list')->with('success', "Supplier \"{$name}\" was deleted.");
    }

    private function allCities(): array
    {
        return collect(config('pakistan_cities'))->flatten()->all();
    }
}
