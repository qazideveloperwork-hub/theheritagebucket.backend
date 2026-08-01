@extends('layout')
@section('title', 'Edit Color')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Settings', 'url' => route('settings.shipping')],
        ['label' => 'Colors', 'url' => route('colors.list')],
        ['label' => 'Edit Color'],
    ]])
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#523923]">Edit Color</h1>
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

    <form action="{{ route('colors.update', $color) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Name</label>
                    <input type="text" name="name" value="{{ old('name', $color->name) }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Swatch color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="hex_code" value="{{ old('hex_code', $color->hex_code ?? '#7C2D12') }}" class="h-11 w-14 cursor-pointer rounded-xl border border-[#d9c9ae] bg-[#faf4ec]" />
                        <span class="text-xs text-[#8a6c56]">Optional — used for the color swatch in dropdowns.</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('colors.list') }}" class="rounded-full border border-[#d9c9ae] px-6 py-3 text-sm text-[#5a4538] hover:bg-[#f1e5d5]">Cancel</a>
            <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save changes</button>
        </div>
    </form>
@endsection
