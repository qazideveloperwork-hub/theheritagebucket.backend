@extends('layout')
@section('title', 'Blog')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Blog'],
    ]])
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-[#523923]">Blog</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="http://theheritagebasket.local/blog" target="_blank" rel="noopener" class="rounded-full border border-[#d9c9ae] px-5 py-3 text-sm font-medium text-[#5a4538] hover:bg-[#f1e5d5]">View live blog</a>
            <a href="{{ route('blog.create') }}" class="rounded-full bg-[#7C2D12] px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">+ Create post</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-[#c7d9c0] bg-[#eef6ea] px-4 py-3 text-sm text-[#3f6a2c]">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-[28px] border border-[#e6d9c5] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#fff7ef] text-xs uppercase tracking-wide text-[#8a6c56]">
                <tr>
                    <th class="px-5 py-3">Cover</th>
                    <th class="px-5 py-3">Title</th>
                    <th class="px-5 py-3">Category</th>
                    <th class="px-5 py-3">Author</th>
                    <th class="px-5 py-3">Published</th>
                    <th class="px-5 py-3">Featured</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f3e6d8]">
                @forelse ($posts as $post)
                    <tr>
                        <td class="px-5 py-3">
                            <img src="{{ $post->cover_image_url }}" alt="" class="h-12 w-12 rounded-xl object-cover" />
                        </td>
                        <td class="px-5 py-3 font-medium text-[#1b1b18]">
                            <a href="{{ route('blog.edit', $post) }}" class="text-[#7c2d12] hover:underline">{{ $post->title }}</a>
                        </td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $post->category ?? '—' }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $post->author_name }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $post->published_at?->format('M j, Y') ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if ($post->is_featured)
                                <span class="rounded-full bg-[#eaf3e5] px-3 py-1 text-xs font-medium text-[#3f6a2c]">Featured</span>
                            @else
                                <span class="text-[#8a6c56]">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('blog.edit', $post) }}" class="rounded-full border border-[#d9c9ae] px-4 py-1.5 text-xs font-medium text-[#5a4538] hover:bg-[#f1e5d5]">Edit</a>
                                <form action="{{ route('blog.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete blog post {{ addslashes($post->title) }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-[#e3b3a3] px-4 py-1.5 text-xs font-medium text-[#7c2d12] hover:bg-[#fdecea]">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-[#8a6c56]">No blog posts yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $posts->links() }}
    </div>
@endsection
