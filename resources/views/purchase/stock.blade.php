@extends('layout')
@section('title', 'Inventory')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Inventory'],
    ]])
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#523923]">Inventory</h1>
        <p class="mt-1 text-sm text-[#8a6c56]">Stock quantities for every product and variation, kept separately from the product details.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-[#c7d9c0] bg-[#eef6ea] px-4 py-3 text-sm text-[#3f6a2c]">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('purchase.stock') }}" class="mb-6 flex flex-wrap items-end gap-4 rounded-[28px] border border-[#e6d9c5] bg-white p-5">
        <div class="min-w-[220px] flex-1">
            <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Search</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Product name or SKU" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-2.5 text-sm outline-none focus:border-[#7c2d12]" />
        </div>
        <button type="submit" class="rounded-full border border-[#d9c9ae] px-5 py-2.5 text-sm text-[#5a4538] hover:bg-[#f1e5d5]">Filter</button>
        @if (request()->filled('q'))
            <a href="{{ route('purchase.stock') }}" class="text-sm text-[#8a6c56] underline">Clear</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-[28px] border border-[#e6d9c5] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#fff7ef] text-xs uppercase tracking-wide text-[#8a6c56]">
                <tr>
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">SKU</th>
                    <th class="px-5 py-3">Stock status</th>
                    <th class="px-5 py-3">Quantity</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f3e6d8]">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-5 py-3 align-top font-medium text-[#1b1b18]">{{ $product->name }}</td>
                        <td class="px-5 py-3 align-top text-[#5a4538]">{{ ucfirst($product->type) }}</td>

                        @if ($product->variations->isEmpty())
                            <td class="px-5 py-3 align-top text-[#5a4538]">{{ $product->sku ?? '—' }}</td>
                            <td class="px-5 py-3 align-top">
                                <span class="rounded-full px-3 py-1 text-xs font-medium {{ $product->stock_status === 'in_stock' ? 'bg-[#eaf3e5] text-[#3f6a2c]' : 'bg-[#fdecea] text-[#7c2d12]' }}">
                                    {{ $product->stock_status === 'in_stock' ? 'In stock' : 'Out of stock' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 align-top">
                                @if ($product->inventory)
                                    <form action="{{ route('inventory.update', $product->inventory) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" min="0" name="quantity" value="{{ old('quantity', $product->inventory->quantity) }}" class="w-24 rounded-xl border border-[#d9c9ae] bg-[#faf4ec] px-3 py-2 text-sm outline-none focus:border-[#7c2d12]" />
                                        <button type="submit" class="rounded-full bg-[#7C2D12] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#6b2610]">Update</button>
                                    </form>
                                @else
                                    <span class="text-[#8a6c56]">—</span>
                                @endif
                            </td>
                        @else
                            <td class="px-5 py-3 align-top text-[#5a4538]">
                                <div class="divide-y divide-[#f3e6d8]">
                                    @foreach ($product->variations as $variation)
                                        <div class="flex h-12 items-center whitespace-nowrap">{{ $variation->sku ?? '—' }}</div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-5 py-3 align-top">
                                <div class="divide-y divide-[#f3e6d8]">
                                    @foreach ($product->variations as $variation)
                                        <div class="flex h-12 items-center">
                                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $variation->stock_status === 'in_stock' ? 'bg-[#eaf3e5] text-[#3f6a2c]' : 'bg-[#fdecea] text-[#7c2d12]' }}">
                                                {{ $variation->stock_status === 'in_stock' ? 'In stock' : 'Out of stock' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-5 py-3 align-top">
                                <div class="divide-y divide-[#f3e6d8]">
                                    @foreach ($product->variations as $variation)
                                        <div class="flex h-12 items-center">
                                            @if ($variation->inventory)
                                                <form action="{{ route('inventory.update', $variation->inventory) }}" method="POST" class="grid grid-cols-[24px_96px_auto] items-center gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <span class="text-xs font-semibold uppercase text-[#8a6c56]">{{ $variation->size?->name ?? '—' }}</span>
                                                    <input type="number" min="0" name="quantity" value="{{ old('quantity', $variation->inventory->quantity) }}" class="w-24 rounded-xl border border-[#d9c9ae] bg-[#faf4ec] px-3 py-2 text-sm outline-none focus:border-[#7c2d12]" />
                                                    <button type="submit" class="rounded-full bg-[#7C2D12] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#6b2610]">Update</button>
                                                </form>
                                            @else
                                                <span class="text-[#8a6c56]">—</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        @endif

                        <td class="px-5 py-3 align-top text-right">
                            <a href="{{ route('products.edit', $product) }}" class="rounded-full border border-[#d9c9ae] px-4 py-1.5 text-xs font-medium text-[#5a4538] hover:bg-[#f1e5d5]">View product</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-sm text-[#8a6c56]">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
@endsection
