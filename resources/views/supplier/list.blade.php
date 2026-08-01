@extends('layout')
@section('title', 'Suppliers')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Stock', 'url' => route('purchase.stock')],
        ['label' => 'Suppliers'],
    ]])
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-[#523923]">Suppliers</h1>
        </div>
        <a href="{{ route('suppliers.create') }}" class="rounded-full bg-[#7C2D12] px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">+ Add supplier</a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-[#c7d9c0] bg-[#eef6ea] px-4 py-3 text-sm text-[#3f6a2c]">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-[28px] border border-[#e6d9c5] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#fff7ef] text-xs uppercase tracking-wide text-[#8a6c56]">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">City</th>
                    <th class="px-5 py-3">Phone</th>
                    <th class="px-5 py-3">Address</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f3e6d8]">
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td class="px-5 py-3 font-medium text-[#1b1b18]">{{ $supplier->name }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $supplier->city ?? '—' }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $supplier->phone ?? '—' }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ \Illuminate\Support\Str::limit($supplier->address, 40) ?: '—' }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="rounded-full border border-[#d9c9ae] px-4 py-1.5 text-xs font-medium text-[#5a4538] hover:bg-[#f1e5d5]">Edit</a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Delete supplier {{ addslashes($supplier->name) }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-[#e3b3a3] px-4 py-1.5 text-xs font-medium text-[#7c2d12] hover:bg-[#fdecea]">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-[#8a6c56]">No suppliers yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $suppliers->links() }}
    </div>
@endsection
