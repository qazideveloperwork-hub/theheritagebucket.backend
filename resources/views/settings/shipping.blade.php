@extends('layout')
@section('title', 'Shipping Cost')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Settings'],
        ['label' => 'Shipping'],
    ]])
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#523923]">Shipping Cost</h1>
        <p class="mt-1 text-sm text-[#8a6c56]">Set a shipping cost for every product. Products with no cost saved yet default to Rs. 250 at checkout.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-[#c7d9c0] bg-[#eef6ea] px-4 py-3 text-sm text-[#3f6a2c]">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('settings.shipping') }}" class="mb-6 flex flex-wrap items-end gap-4 rounded-[28px] border border-[#e6d9c5] bg-white p-5">
        <div class="min-w-[220px] flex-1">
            <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Search</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Product name or SKU" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-2.5 text-sm outline-none focus:border-[#7c2d12]" />
        </div>
        <button type="submit" class="rounded-full border border-[#d9c9ae] px-5 py-2.5 text-sm text-[#5a4538] hover:bg-[#f1e5d5]">Filter</button>
        @if (request()->filled('q'))
            <a href="{{ route('settings.shipping') }}" class="text-sm text-[#8a6c56] underline">Clear</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-[28px] border border-[#e6d9c5] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#fff7ef] text-xs uppercase tracking-wide text-[#8a6c56]">
                <tr>
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3">SKU</th>
                    <th class="px-5 py-3">Shipping cost</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f3e6d8]">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-5 py-3 font-medium text-[#1b1b18]">{{ $product->name }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $product->sku ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <form action="{{ route('settings.shipping.update', $product) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <span class="text-[#8a6c56]">Rs.</span>
                                <input type="number" step="0.01" min="0" name="shipping_cost" value="{{ old('shipping_cost', $product->shippingCost->shipping_cost ?? 250) }}" class="w-28 rounded-xl border border-[#d9c9ae] bg-[#faf4ec] px-3 py-2 text-sm outline-none focus:border-[#7c2d12]" />
                                <button type="submit" class="rounded-full bg-[#7C2D12] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#6b2610]">Update</button>
                            </form>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('products.edit', $product) }}" class="rounded-full border border-[#d9c9ae] px-4 py-1.5 text-xs font-medium text-[#5a4538] hover:bg-[#f1e5d5]">View product</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-sm text-[#8a6c56]">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
@endsection
