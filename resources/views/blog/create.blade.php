@extends('layout')
@section('title', 'Create Blog Post')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Blog', 'url' => route('blog.list')],
        ['label' => 'Create Post'],
    ]])
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#523923]">Create Blog Post</h1>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-[#e3b3a3] bg-[#fdecea] px-4 py-3 text-sm text-[#7c2d12]">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('blog.store') }}" method="POST" class="space-y-6">
        @csrf
        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Title <span class="text-[#b91c1c]">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" placeholder="A Journey Through Handwoven Craft" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Excerpt <span class="text-[#b91c1c]">*</span></label>
                    <textarea name="excerpt" rows="2" required maxlength="255" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" placeholder="Short summary shown on the blog listing">{{ old('excerpt') }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Body <span class="text-[#b91c1c]">*</span></label>
                    <textarea id="body" name="body" rows="10">{{ old('body') }}</textarea>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Cover image URL <span class="text-[#b91c1c]">*</span></label>
                    <input type="url" name="cover_image_url" value="{{ old('cover_image_url') }}" required maxlength="2048" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" placeholder="https://..." />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Category</label>
                    <input type="text" name="category" value="{{ old('category') }}" maxlength="255" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Author name <span class="text-[#b91c1c]">*</span></label>
                    <input type="text" name="author_name" value="{{ old('author_name') }}" required maxlength="255" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Author image URL</label>
                    <input type="url" name="author_image_url" value="{{ old('author_image_url') }}" maxlength="2048" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" placeholder="https://..." />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Read minutes <span class="text-[#b91c1c]">*</span></label>
                    <input type="number" name="read_minutes" value="{{ old('read_minutes', 5) }}" required min="1" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Published at</label>
                    <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div class="flex items-center gap-2 sm:col-span-2">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured')) class="h-4 w-4 rounded border-[#d9c9ae]" />
                    <label for="is_featured" class="text-sm font-medium text-[#5a4538]">Featured post</label>
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('blog.list') }}" class="rounded-full border border-[#d9c9ae] px-6 py-3 text-sm text-[#5a4538] hover:bg-[#f1e5d5]">Cancel</a>
            <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save post</button>
        </div>
    </form>

    <script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
    <script>
        if (window.CKEDITOR) {
            CKEDITOR.config.versionCheck = false;
            CKEDITOR.replace('body');
        }
    </script>
@endsection
