<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->withCount('products')
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->input('status')),
                fn ($query) => $query->where('status', '!=', 'deleted')
            )
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $statusCounts = [
            'all' => Category::where('status', '!=', 'deleted')->count(),
            'publish' => Category::where('status', 'publish')->count(),
            'draft' => Category::where('status', 'draft')->count(),
            'deleted' => Category::where('status', 'deleted')->count(),
        ];

        return view('category.list', compact('categories', 'statusCounts'));
    }

    public function create(): View
    {
        return view('category.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cat_code' => ['required', 'string', 'max:50', Rule::unique('categories', 'cat_code')],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:publish,draft'],
        ]);

        $data['slug'] = $this->generateSlug($data['name']);

        Category::create($data);

        return redirect()->route('categories.list')->with('success', "Category \"{$data['name']}\" was created.");
    }

    public function edit(Category $category): View
    {
        return view('category.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cat_code' => ['required', 'string', 'max:50', Rule::unique('categories', 'cat_code')->ignore($category->id)],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:publish,draft,deleted'],
        ]);

        if ($data['name'] !== $category->name) {
            $data['slug'] = $this->generateSlug($data['name'], $category);
        }

        $category->update($data);

        return redirect()->route('categories.list')->with('success', "Category \"{$category->name}\" was updated.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->update(['status' => 'deleted']);

        return redirect()->route('categories.list')->with('success', "Category \"{$category->name}\" was deleted.");
    }

    private function generateSlug(string $name, ?Category $ignore = null): string
    {
        $slugBase = Str::slug($name);
        $slug = $slugBase;
        $suffix = 2;

        while (Category::where('slug', $slug)->when($ignore, fn ($query) => $query->where('id', '!=', $ignore->id))->exists()) {
            $slug = "{$slugBase}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
