@extends('layout')
@section('title', 'Category Slider')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Category Slider'],
    ]])
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#523923]">Category Slider</h1>
        <p class="mt-1 text-sm text-[#8a6c56]">Categories featured in the "Shop by Category" section on the homepage, each with its own tile image.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-[#c7d9c0] bg-[#eef6ea] px-4 py-3 text-sm text-[#3f6a2c]">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-[#e3b3a3] bg-[#fdecea] px-4 py-3 text-sm text-[#7c2d12]">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="mb-6 rounded-[28px] border border-[#e6d9c5] bg-white p-6">
        <h2 class="text-lg font-semibold text-[#523923]">Add a category</h2>
        <form action="{{ route('sliders.category.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[220px]">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Category</label>
                    <select name="category_id" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">
                        <option value="">Select a category</option>
                        @foreach ($availableCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    @php $oldTileImage = old('image_media_id') ? $mediaLibrary->firstWhere('id', (int) old('image_media_id')) : null; @endphp
                    <img id="category-tile-image-preview" @if ($oldTileImage) src="{{ $oldTileImage->thumbnailUrl() }}" @endif alt="Tile image preview" class="mb-2 h-14 w-14 rounded-xl object-cover {{ $oldTileImage ? '' : 'hidden' }}" />
                    <input type="hidden" name="image_media_id" id="category-tile-image-media-id" value="{{ old('image_media_id') }}" />
                    <button type="button" class="rounded-full bg-[#7C2D12] px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]" onclick="openMediaPicker('single', function (items) { setSingleImage('category-tile-image-media-id', 'category-tile-image-preview', items[0]); })">Choose image</button>
                </div>
            </div>
            <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Add</button>
        </form>
    </section>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
        @forelse ($slides as $slide)
            <div class="overflow-hidden rounded-2xl border border-[#e6d9c5] bg-white">
                <div class="aspect-square w-full bg-[#faf4ec]">
                    @if ($slide->image)
                        <img src="{{ $slide->image->thumbnailUrl() }}" alt="{{ $slide->category?->name }}" class="h-full w-full object-cover" />
                    @endif
                </div>
                <div class="flex items-center justify-between p-3">
                    <span class="text-sm font-medium text-[#1b1b18]">{{ $slide->category?->name ?? 'Deleted category' }}</span>
                    <form action="{{ route('sliders.category.destroy', $slide) }}" method="POST" onsubmit="return confirm('Remove {{ addslashes($slide->category?->name ?? 'this category') }} from the slider?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-full border border-[#e3b3a3] px-3 py-1 text-xs font-medium text-[#7c2d12] hover:bg-[#fdecea]">Remove</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-sm text-[#8a6c56]">No categories added yet.</p>
        @endforelse
    </div>

    @include('partials.media-picker')
@endsection
