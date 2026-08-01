@extends('layout')
@section('title', 'Edit Supplier')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Stock', 'url' => route('purchase.stock')],
        ['label' => 'Suppliers', 'url' => route('suppliers.list')],
        ['label' => 'Edit Supplier'],
    ]])
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#523923]">Edit Supplier</h1>
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

    <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Supplier name</label>
                    <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">City</label>
                    <select name="city" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">
                        <option value="">Select a city</option>
                        @foreach ($cities as $province => $provinceCities)
                            <optgroup label="{{ $province }}">
                                @foreach ($provinceCities as $city)
                                    <option value="{{ $city }}" @selected(old('city', $supplier->city) === $city)>{{ $city }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Address</label>
                    <textarea name="address" rows="2" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">{{ old('address', $supplier->address) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Description</label>
                    <textarea name="description" rows="3" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">{{ old('description', $supplier->description) }}</textarea>
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('suppliers.list') }}" class="rounded-full border border-[#d9c9ae] px-6 py-3 text-sm text-[#5a4538] hover:bg-[#f1e5d5]">Cancel</a>
            <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save changes</button>
        </div>
    </form>
@endsection
