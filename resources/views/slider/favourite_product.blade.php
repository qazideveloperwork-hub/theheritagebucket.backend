@extends('layout')
@section('title', 'Favourite Product')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Favourite Product'],
    ]])
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#523923]">Favourite Product</h1>
        <p class="mt-1 text-sm text-[#8a6c56]">Products featured in the "Favourite Products" section on the homepage.</p>
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

    <section class="mb-6 rounded-[28px] border border-[#e6d9c5] bg-white p-6">
        <h2 class="text-lg font-semibold text-[#523923]">Add a product</h2>
        <form action="{{ route('sliders.favourite-product.store') }}" method="POST" class="mt-4 flex flex-wrap items-center gap-3">
            @csrf
            <select name="product_id" required class="w-full max-w-sm rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">
                <option value="">Select a product</option>
                @foreach ($availableProducts as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Add</button>
        </form>
    </section>

    <div class="overflow-hidden rounded-[28px] border border-[#e6d9c5] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#fff7ef] text-xs uppercase tracking-wide text-[#8a6c56]">
                <tr>
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f3e6d8]">
                @forelse ($slides as $slide)
                    <tr>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if ($slide->product?->cover_image_url)
                                    <img src="{{ $slide->product->cover_image_url }}" alt="{{ $slide->product->name }}" class="h-10 w-10 rounded-lg object-cover" />
                                @endif
                                <span class="font-medium text-[#1b1b18]">{{ $slide->product?->name ?? 'Deleted product' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <form action="{{ route('sliders.favourite-product.destroy', $slide) }}" method="POST" onsubmit="return confirm('Remove {{ addslashes($slide->product?->name ?? 'this product') }} from Favourite Products?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full border border-[#e3b3a3] px-4 py-1.5 text-xs font-medium text-[#7c2d12] hover:bg-[#fdecea]">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-5 py-10 text-center text-sm text-[#8a6c56]">No products added yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
