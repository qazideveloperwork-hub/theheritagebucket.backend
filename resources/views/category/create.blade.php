@extends('layout')
@section('title', 'Create Category')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Categories', 'url' => route('categories.list')],
        ['label' => 'Create Category'],
    ]])
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#523923]">Create Category</h1>
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

    <form action="{{ route('categories.store') }}" method="POST" class="space-y-6">
        @csrf
        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Name <span class="text-[#b91c1c]">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" placeholder="Blue Pottery" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Category code <span class="text-[#b91c1c]">*</span></label>
                    <input type="text" name="cat_code" value="{{ old('cat_code') }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" placeholder="e.g. CAT-001" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Status <span class="text-[#b91c1c]">*</span></label>
                    <select name="status" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">
                        <option value="publish" @selected(old('status', 'publish') === 'publish')>Publish</option>
                        <option value="draft" @selected(old('status') === 'draft')>Draft</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Description</label>
                    <textarea name="description" rows="4" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">{{ old('description') }}</textarea>
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('categories.list') }}" class="rounded-full border border-[#d9c9ae] px-6 py-3 text-sm text-[#5a4538] hover:bg-[#f1e5d5]">Cancel</a>
            <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save category</button>
        </div>
    </form>
@endsection
