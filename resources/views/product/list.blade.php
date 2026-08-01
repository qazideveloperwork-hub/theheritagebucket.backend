@extends('layout')
@section('title', 'Products - List')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Products'],
    ]])
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-[#523923]">Products</h1>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('products.export') }}" class="rounded-full border border-[#d9c9ae] px-5 py-3 text-sm font-medium text-[#5a4538] hover:bg-[#f1e5d5]">Export</a>
            <a href="{{ route('products.import') }}" class="rounded-full border border-[#d9c9ae] px-5 py-3 text-sm font-medium text-[#5a4538] hover:bg-[#f1e5d5]">Import</a>
            <a href="{{ route('products.create') }}" class="rounded-full bg-[#7C2D12] px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">+ Create product</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-[#c7d9c0] bg-[#eef6ea] px-4 py-3 text-sm text-[#3f6a2c]">
            {{ session('success') }}
        </div>
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
            <a href="{{ route('products.list', array_merge(request()->except(['status', 'page']), $tab['status'] ? ['status' => $tab['status']] : [])) }}"
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

    <form method="GET" action="{{ route('products.list') }}" class="mb-6 flex flex-wrap items-end gap-4 rounded-[28px] border border-[#e6d9c5] bg-white p-5">
        <div class="min-w-[220px] flex-1">
            <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Search</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Name or SKU" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-2.5 text-sm outline-none focus:border-[#7c2d12]" />
        </div>
        <div class="min-w-[180px]">
            <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Category</label>
            <select name="category" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-2.5 text-sm outline-none focus:border-[#7c2d12]">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[160px]">
            <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Status</label>
            <select name="status" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-2.5 text-sm outline-none focus:border-[#7c2d12]">
                <option value="">All statuses</option>
                <option value="publish" @selected(request('status') === 'publish')>Published</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                <option value="deleted" @selected(request('status') === 'deleted')>Deleted</option>
            </select>
        </div>
        <button type="submit" class="rounded-full border border-[#d9c9ae] px-5 py-2.5 text-sm text-[#5a4538] hover:bg-[#f1e5d5]">Filter</button>
        @if (request()->anyFilled(['q', 'category', 'status']))
            <a href="{{ route('products.list') }}" class="text-sm text-[#8a6c56] underline">Clear</a>
        @endif
    </form>

    <div class="rounded-[28px] border border-[#e6d9c5] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="text-xs uppercase tracking-wide text-[#8a6c56]">
                <tr>
                    <th class="sticky top-[71px] z-10 rounded-tl-[28px] bg-[#fff7ef] px-5 py-3">Product</th>
                    <th class="sticky top-[71px] z-10 bg-[#fff7ef] px-5 py-3">Category</th>
                    <th class="sticky top-[71px] z-10 bg-[#fff7ef] px-5 py-3">SKU</th>
                    <th class="sticky top-[71px] z-10 bg-[#fff7ef] px-5 py-3">Price</th>
                    <th class="sticky top-[71px] z-10 bg-[#fff7ef] px-5 py-3">Discount %</th>
                    <th class="sticky top-[71px] z-10 bg-[#fff7ef] px-5 py-3">Sale price</th>
                    <th class="sticky top-[71px] z-10 bg-[#fff7ef] px-5 py-3">Stock</th>
                    <th class="sticky top-[71px] z-10 bg-[#fff7ef] px-5 py-3">Status</th>
                    <th class="sticky top-[71px] z-10 rounded-tr-[28px] bg-[#fff7ef] px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f3e6d8]">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" class="h-12 w-12 shrink-0 rounded-xl object-cover" />
                                <div>
                                    <a href="{{ rtrim(config('services.storefront.url'), '/') }}/products/{{ $product->slug }}" target="_blank" rel="noopener" class="font-medium text-[#1b1b18] hover:text-[#7c2d12] hover:underline">{{ $product->name }}</a>
                                    @if ($product->isVariable())
                                        <p class="text-xs text-[#8a6c56]">Variable — {{ $product->variations->count() }} variation{{ $product->variations->count() === 1 ? '' : 's' }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $product->categories->pluck('name')->join(', ') ?: '—' }}</td>
                        <td class="whitespace-nowrap px-5 py-3 text-[#5a4538]">
                            @if ($product->isVariable())
                                <div class="space-y-0.5 text-xs">
                                    @foreach ($product->variations as $variation)
                                        <p class="grid grid-cols-[24px_auto] gap-1">
                                            <span class="font-medium text-[#1b1b18]">{{ $variation->size?->name ?? '—' }}:</span>
                                            <span>{{ $variation->sku }}</span>
                                        </p>
                                    @endforeach
                                </div>
                            @else
                                {{ $product->sku }}
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if ($product->isVariable())
                                <div class="space-y-0.5 text-xs">
                                    @foreach ($product->variations as $variation)
                                        <p>
                                            <span class="font-medium text-[#1b1b18]">{{ $variation->size?->name ?? '—' }}:</span>
                                            @if ($variation->discount_price)
                                                <span class="text-[#7c2d12] font-medium">Rs. {{ number_format($variation->discount_price, 2) }}</span>
                                                <span class="text-[#8a6c56] line-through">Rs. {{ number_format($variation->regular_price, 2) }}</span>
                                            @else
                                                Rs. {{ number_format($variation->regular_price, 2) }}
                                            @endif
                                        </p>
                                    @endforeach
                                </div>
                            @elseif ($product->discount_price)
                                <span class="text-[#7c2d12] font-medium">Rs. {{ number_format($product->discount_price, 2) }}</span>
                                <span class="ml-1 text-xs text-[#8a6c56] line-through">Rs. {{ number_format($product->regular_price, 2) }}</span>
                            @else
                                <span class="text-[#1b1b18]">Rs. {{ number_format($product->regular_price, 2) }}</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-5 py-3 text-[#5a4538]">
                            @if ($product->isVariable())
                                <div class="space-y-0.5 text-xs">
                                    @foreach ($product->variations as $variation)
                                        <p class="grid grid-cols-[24px_auto] gap-1">
                                            <span class="font-medium text-[#1b1b18]">{{ $variation->size?->name ?? '—' }}:</span>
                                            <span>{{ $variation->discount_percentage ? number_format($variation->discount_percentage, 2) . '%' : '—' }}</span>
                                        </p>
                                    @endforeach
                                </div>
                            @else
                                {{ $product->discount_percentage ? number_format($product->discount_percentage, 2) . '%' : '—' }}
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-5 py-3 text-[#5a4538]">
                            @if ($product->isVariable())
                                <div class="space-y-0.5 text-xs">
                                    @foreach ($product->variations as $variation)
                                        <p class="grid grid-cols-[24px_auto] gap-1">
                                            <span class="font-medium text-[#1b1b18]">{{ $variation->size?->name ?? '—' }}:</span>
                                            <span>{{ $variation->sale_price ? 'Rs. ' . number_format($variation->sale_price, 2) : '—' }}</span>
                                        </p>
                                    @endforeach
                                </div>
                            @else
                                {{ $product->sale_price ? 'Rs. ' . number_format($product->sale_price, 2) : '—' }}
                            @endif
                        </td>
                        <td class="px-5 py-3 text-[#5a4538]">
                            @php
                                $variationStockTotal = $product->variations->sum(fn ($variation) => optional($variation->inventory)->quantity ?? 0);
                            @endphp
                            @if ($product->isVariable())
                                <div class="space-y-0.5 text-xs">
                                    @foreach ($product->variations as $variation)
                                        <p><span class="font-medium text-[#1b1b18]">{{ $variation->size?->name ?? '—' }}:</span> {{ optional($variation->inventory)->quantity ?? 0 }}</p>
                                    @endforeach
                                    <p class="font-semibold text-[#1b1b18]">Total: {{ $variationStockTotal }}</p>
                                    <p class="font-medium {{ $variationStockTotal > 0 ? 'text-[#3f6a2c]' : 'text-[#7c2d12]' }}">
                                        {{ $variationStockTotal > 0 ? 'In Stock' : 'Out of stock' }}
                                    </p>
                                </div>
                            @else
                                <p>{{ optional($product->inventory)->quantity ?? 0 }}</p>
                                <p class="text-xs font-medium {{ $product->stock_status === 'in_stock' ? 'text-[#3f6a2c]' : 'text-[#7c2d12]' }}">
                                    {{ $product->stock_status === 'in_stock' ? 'In Stock' : 'Out of stock' }}
                                </p>
                            @endif
                        </td>
                        <td class="px-5 py-3">
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
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusStyles[$product->status] ?? 'bg-[#f1e5d5] text-[#8a6c56]' }}">
                                {{ $statusLabels[$product->status] ?? ucfirst($product->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="inline-flex flex-col items-stretch gap-2">
                                <a href="{{ route('products.edit', $product) }}" class="rounded-full border border-[#d9c9ae] px-4 py-1.5 text-center text-xs font-medium text-[#5a4538] hover:bg-[#f1e5d5]">Edit</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete {{ addslashes($product->name) }}? It will be marked as Deleted and hidden from this list.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full rounded-full border border-[#e3b3a3] px-4 py-1.5 text-center text-xs font-medium text-[#7c2d12] hover:bg-[#fdecea]">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-5 py-10 text-center text-sm text-[#8a6c56]">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
@endsection
