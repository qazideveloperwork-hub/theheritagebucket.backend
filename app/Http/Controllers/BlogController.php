<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::orderByDesc('published_at')->orderByDesc('id')->paginate(20);

        return view('blog.list', compact('posts'));
    }

    public function create(): View
    {
        return view('blog.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['slug'] = $this->generateSlug($data['title']);

        BlogPost::create($data);

        return redirect()->route('blog.list')->with('success', "Blog post \"{$data['title']}\" was created.");
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('blog.edit', compact('blogPost'));
    }

    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $data = $this->validateData($request);

        if ($data['title'] !== $blogPost->title) {
            $data['slug'] = $this->generateSlug($data['title'], $blogPost);
        }

        $blogPost->update($data);

        return redirect()->route('blog.list')->with('success', "Blog post \"{$blogPost->title}\" was updated.");
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $title = $blogPost->title;
        $blogPost->delete();

        return redirect()->route('blog.list')->with('success', "Blog post \"{$title}\" was deleted.");
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'cover_image_url' => ['required', 'url', 'max:2048'],
            'category' => ['nullable', 'string', 'max:255'],
            'author_name' => ['required', 'string', 'max:255'],
            'author_image_url' => ['nullable', 'url', 'max:2048'],
            'read_minutes' => ['required', 'integer', 'min:1'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');

        return $data;
    }

    private function generateSlug(string $title, ?BlogPost $ignore = null): string
    {
        $slugBase = Str::slug($title);
        $slug = $slugBase;
        $suffix = 2;

        while (BlogPost::where('slug', $slug)->when($ignore, fn ($query) => $query->where('id', '!=', $ignore->id))->exists()) {
            $slug = "{$slugBase}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
