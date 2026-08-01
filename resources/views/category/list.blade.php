@extends('layout')
@section('title', 'Categories')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Categories'],
    ]])
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-[#523923]">Categories</h1>
        </div>
        <a href="{{ route('categories.create') }}" class="rounded-full bg-[#7C2D12] px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">+ Create category</a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-[#c7d9c0] bg-[#eef6ea] px-4 py-3 text-sm text-[#3f6a2c]">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-2xl border border-[#e3b3a3] bg-[#fdecea] px-4 py-3 text-sm text-[#7c2d12]">{{ session('error') }}</div>
    @endif

    @php
        $statusTabs = [
            'all' => ['label' => 'All', 'status' => null],
            'publish' => ['label' => 'Published', 'status' => 'publish'],
            'draft' => ['label' => 'Draft', 'status' => 'draft'],
            'deleted' => ['label' => 'Deleted', 'status' => 'deleted'],
        ];
        $activeStatus = request('status');
    @endphp
    <div class="mb-6 flex flex-wrap items-center gap-2 rounded-[28px] border border-[#e6d9c5] bg-white px-5 py-4 text-sm">
        @foreach ($statusTabs as $key => $tab)
            @php $isActive = $activeStatus === $tab['status'] || ($tab['status'] === null && ! $activeStatus); @endphp
            <a href="{{ route('categories.list', array_merge(request()->except(['status', 'page']), $tab['status'] ? ['status' => $tab['status']] : [])) }}"
               class="flex items-center gap-2 rounded-full px-3 py-1.5 {{ $isActive ? 'bg-[#fdf1e6] font-semibold text-[#7c2d12]' : 'text-[#5a4538] hover:bg-[#f1e5d5]' }}">
                {{ $tab['label'] }}
                <span class="inline-flex min-w-[1.625rem] items-center justify-center rounded-full bg-[#7C2D12] px-2 py-1 text-xs font-semibold leading-none text-white">
                    {{ $statusCounts[$key] }}
                </span>
            </a>
            @if (! $loop->last)
                <span class="text-[#e6d9c5]">|</span>
            @endif
        @endforeach
    </div>

    <div class="overflow-hidden rounded-[28px] border border-[#e6d9c5] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#fff7ef] text-xs uppercase tracking-wide text-[#8a6c56]">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Code</th>
                    <th class="px-5 py-3">Description</th>
                    <th class="px-5 py-3">Products</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f3e6d8]">
                @forelse ($categories as $category)
                    @php
                        $statusStyles = [
                            'publish' => 'bg-[#eaf3e5] text-[#3f6a2c]',
                            'draft' => 'bg-[#f1e5d5] text-[#8a6c56]',
                            'deleted' => 'bg-[#fdecea] text-[#b91c1c]',
                        ];
                        $statusLabels = [
                            'publish' => 'Published',
                            'draft' => 'Draft',
                            'deleted' => 'Deleted',
                        ];
                    @endphp
                    <tr>
                        <td class="px-5 py-3 font-medium text-[#1b1b18]">{{ $category->name }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $category->cat_code ?? '—' }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ Str::limit($category->description, 80) ?: '—' }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $category->products_count }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusStyles[$category->status] ?? 'bg-[#f1e5d5] text-[#8a6c56]' }}">
                                {{ $statusLabels[$category->status] ?? ucfirst($category->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('categories.edit', $category) }}" class="rounded-full border border-[#d9c9ae] px-4 py-1.5 text-xs font-medium text-[#5a4538] hover:bg-[#f1e5d5]">Edit</a>
                                @if ($category->status !== 'deleted')
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete {{ addslashes($category->name) }}? It will be marked as Deleted and hidden from this list.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-full border border-[#e3b3a3] px-4 py-1.5 text-xs font-medium text-[#7c2d12] hover:bg-[#fdecea]">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-sm text-[#8a6c56]">No categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $categories->links() }}
    </div>
@endsection
