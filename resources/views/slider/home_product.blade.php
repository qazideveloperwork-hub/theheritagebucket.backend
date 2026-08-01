@extends('layout')
@section('title', 'Home banner')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Home banner'],
    ]])
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#523923]">Home banner</h1>
        <p class="mt-1 text-sm text-[#8a6c56]">Content shown in the hero banner at the top of the homepage.</p>
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

    <form action="{{ route('sliders.home.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Banner image</label>
                    <img id="banner-image-preview" @if ($slider->image) src="{{ $slider->image->thumbnailUrl() }}" @endif alt="Banner image preview" class="mb-2 h-24 w-24 rounded-xl object-cover {{ $slider->image ? '' : 'hidden' }}" />
                    <input type="hidden" name="image_media_id" id="banner-image-media-id" value="{{ old('image_media_id', $slider->image_id) }}" />
                    <button type="button" class="w-full max-w-xs rounded-full bg-[#7C2D12] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]" onclick="openMediaPicker('single', function (items) { setSingleImage('banner-image-media-id', 'banner-image-preview', items[0]); })">Choose from Library</button>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Heading 1</label>
                    <input type="text" name="heading_1" value="{{ old('heading_1', $slider->heading_1) }}" placeholder="Exquisite Craftsmanship" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Heading 2</label>
                    <input type="text" name="heading_2" value="{{ old('heading_2', $slider->heading_2) }}" placeholder="Preserving the Soul of Ancient Artistry" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Description</label>
                    <textarea name="description" rows="4" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">{{ old('description', $slider->description) }}</textarea>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Button text</label>
                    <input type="text" name="button_text" value="{{ old('button_text', $slider->button_text) }}" placeholder="Shop the Collection" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Button link</label>
                    <input type="text" name="button_url" value="{{ old('button_url', $slider->button_url) }}" placeholder="/products" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save changes</button>
        </div>
    </form>

    @include('partials.media-picker')
@endsection
