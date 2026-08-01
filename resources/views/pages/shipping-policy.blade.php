@extends('layout')
@section('title', 'Shipping Policy')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Pages'],
        ['label' => 'Shipping Policy'],
    ]])
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#523923]">Shipping Policy</h1>
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

    <form action="{{ route('pages.shipping-policy.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <div class="grid gap-5">
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Title <span class="text-[#b91c1c]">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $page->title) }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Content</label>
                    <textarea id="content" name="content" rows="14">{{ old('content', $page->content) }}</textarea>
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save changes</button>
        </div>
    </form>

    <script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
    <script>
        if (window.CKEDITOR) {
            CKEDITOR.config.versionCheck = false;
            CKEDITOR.replace('content');
        }
    </script>
@endsection
