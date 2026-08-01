@extends('layout')
@section('title', 'Create User')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Users', 'url' => route('users.index')],
        ['label' => 'Create'],
    ]])

    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#523923]">Create User</h1>
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

    <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
        @csrf
        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <h2 class="mb-4 text-lg font-semibold text-[#523923]">Account</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Full name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Password</label>
                    <input type="password" name="password" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
            </div>
        </section>

        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <h2 class="mb-4 text-lg font-semibold text-[#523923]">Address</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Street address</label>
                    <input type="text" name="street" value="{{ old('street') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Shipping address</label>
                    <input type="text" name="address_line_2" value="{{ old('address_line_2') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">City</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Province</label>
                    <select name="province" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]">
                        <option value="" @selected(! old('province'))>Select</option>
                        @foreach (['punjab' => 'Punjab', 'sindh' => 'Sindh', 'kpk' => 'KPK', 'balochistan' => 'Balochistan', 'ict' => 'Islamabad Capital Territory'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('province') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Postal code</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('users.index') }}" class="rounded-full border border-[#d9c9ae] px-6 py-3 text-sm text-[#5a4538] hover:bg-[#f1e5d5]">Cancel</a>
            <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save user</button>
        </div>
    </form>
@endsection
