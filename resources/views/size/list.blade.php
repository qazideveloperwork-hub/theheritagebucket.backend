@extends('layout')
@section('title', 'Sizes')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Settings', 'url' => route('settings.shipping')],
        ['label' => 'Sizes'],
    ]])
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-[#523923]">Sizes</h1>
        </div>
        <a href="{{ route('sizes.create') }}" class="rounded-full bg-[#7C2D12] px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">+ Add size</a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-[#c7d9c0] bg-[#eef6ea] px-4 py-3 text-sm text-[#3f6a2c]">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-2xl border border-[#e3b3a3] bg-[#fdecea] px-4 py-3 text-sm text-[#7c2d12]">{{ session('error') }}</div>
    @endif

    <div class="overflow-hidden rounded-[28px] border border-[#e6d9c5] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#fff7ef] text-xs uppercase tracking-wide text-[#8a6c56]">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Sort order</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f3e6d8]">
                @forelse ($sizes as $size)
                    <tr>
                        <td class="px-5 py-3 font-medium text-[#1b1b18]">{{ $size->name }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $size->sort_order }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('sizes.edit', $size) }}" class="rounded-full border border-[#d9c9ae] px-4 py-1.5 text-xs font-medium text-[#5a4538] hover:bg-[#f1e5d5]">Edit</a>
                                <form action="{{ route('sizes.destroy', $size) }}" method="POST" onsubmit="return confirm('Delete size {{ addslashes($size->name) }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-[#e3b3a3] px-4 py-1.5 text-xs font-medium text-[#7c2d12] hover:bg-[#fdecea]">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-10 text-center text-sm text-[#8a6c56]">No sizes yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $sizes->links() }}
    </div>
@endsection
