@extends('layout')
@section('title', 'Artisan Story')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Artisan Story'],
    ]])
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#523923]">Artisan Story</h1>
        <p class="mt-1 text-sm text-[#8a6c56]">Content shown in the "Meet Our Hands" section on the homepage.</p>
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

    <form action="{{ route('sliders.artisan-story.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Artisan photo</label>
                    <img id="artisan-story-image-preview" @if ($slider->image) src="{{ $slider->image->thumbnailUrl() }}" @endif alt="Artisan photo preview" class="mb-2 h-24 w-24 rounded-xl object-cover {{ $slider->image ? '' : 'hidden' }}" />
                    <input type="hidden" name="image_media_id" id="artisan-story-image-media-id" value="{{ old('image_media_id', $slider->image_id) }}" />
                    <button type="button" class="w-full max-w-xs rounded-full bg-[#7C2D12] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]" onclick="openMediaPicker('single', function (items) { setSingleImage('artisan-story-image-media-id', 'artisan-story-image-preview', items[0]); })">Choose from Library</button>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Heading 1</label>
                    <input type="text" name="heading_1" value="{{ old('heading_1', $slider->heading_1) }}" placeholder="Meet Our Hands" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Heading 2</label>
                    <input type="text" name="heading_2" value="{{ old('heading_2', $slider->heading_2) }}" placeholder="The Artisan Story" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Description</label>
                    <textarea name="description" rows="4" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">{{ old('description', $slider->description) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Quote <span class="text-xs text-[#8a6c56]">(shown in italics)</span></label>
                    <textarea name="quote" rows="3" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">{{ old('quote', $slider->quote) }}</textarea>
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save changes</button>
        </div>
    </form>

    @include('partials.media-picker')
@endsection
