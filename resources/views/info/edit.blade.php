@extends('layout')
@section('title', 'Edit Info')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Settings', 'url' => route('settings.shipping')],
        ['label' => 'Info Library', 'url' => route('infos.list')],
        ['label' => 'Edit Info'],
    ]])
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#523923]">Edit Info</h1>
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

    <form action="{{ route('infos.update', $info) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Title</label>
                    <input type="text" name="title" value="{{ old('title', $info->title) }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('infos.list') }}" class="rounded-full border border-[#d9c9ae] px-6 py-3 text-sm text-[#5a4538] hover:bg-[#f1e5d5]">Cancel</a>
            <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save changes</button>
        </div>
    </form>
@endsection
