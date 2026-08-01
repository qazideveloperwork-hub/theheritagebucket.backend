@extends('layout')
@section('title', 'Options')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Settings'],
        ['label' => 'Options'],
    ]])

    <h1 class="text-2xl font-semibold text-[#523923] mb-6">Options</h1>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-[#c7d9c0] bg-[#eef6ea] px-4 py-3 text-sm text-[#3f6a2c]">{{ session('success') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2 items-start">
        <div class="space-y-6">
            <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
                <h2 class="text-lg font-semibold text-[#523923]">General</h2>
                <p class="mt-1 text-xs text-[#8a6c56]">Used to complete product/slider image or link fields that are saved as a relative path (e.g. "/storage/media/xyz.jpg") instead of a full URL. Change this once here if this admin site ever moves to a different domain.</p>
                <form action="{{ route('settings.options.update') }}" method="POST" class="mt-4 space-y-5" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="general">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#5a4538]">Base URL</label>
                        <input type="url" name="base_url" value="{{ old('base_url', $setting->base_url) }}" placeholder="{{ config('app.url') }}" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                        <p class="mt-1 text-xs text-[#8a6c56]">Leave blank to use this site's own URL ({{ config('app.url') }}).</p>
                        @error('base_url') <p class="mt-1 text-xs text-[#b91c1c]">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save general settings</button>
                </form>
            </section>

            <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
                <h2 class="text-lg font-semibold text-[#523923]">WhatsApp</h2>
                <p class="mt-1 text-xs text-[#8a6c56]">Shown to customers on the storefront (WhatsApp icon on product pages &amp; checkout).</p>
                <form action="{{ route('settings.options.update') }}" method="POST" class="mt-4 space-y-5" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="whatsapp">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#5a4538]">WhatsApp number <span class="text-[#b91c1c]">*</span></label>
                        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $setting->whatsapp_number) }}" required placeholder="923001234567" class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                        <p class="mt-1 text-xs text-[#8a6c56]">With country code, no spaces or leading +.</p>
                        @error('whatsapp_number') <p class="mt-1 text-xs text-[#b91c1c]">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save WhatsApp</button>
                </form>
            </section>

            <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
                <h2 class="text-lg font-semibold text-[#523923]">Email</h2>
                <p class="mt-1 text-xs text-[#8a6c56]">Contact email shown to customers.</p>
                <form action="{{ route('settings.options.update') }}" method="POST" class="mt-4 space-y-5" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="email">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#5a4538]">Contact email <span class="text-[#b91c1c]">*</span></label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $setting->contact_email) }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                        @error('contact_email') <p class="mt-1 text-xs text-[#b91c1c]">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save email</button>
                </form>
            </section>

            <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
                <h2 class="text-lg font-semibold text-[#523923]">Cart</h2>
                <p class="mt-1 text-xs text-[#8a6c56]">Limits applied to every product added to the storefront cart.</p>
                <form action="{{ route('settings.options.update') }}" method="POST" class="mt-4 space-y-5" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="cart">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#5a4538]">Max quantity per product <span class="text-[#b91c1c]">*</span></label>
                        <input type="number" min="1" max="100" name="max_quantity_per_product" value="{{ old('max_quantity_per_product', $setting->max_quantity_per_product) }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                        <p class="mt-1 text-xs text-[#8a6c56]">A customer can never add more than this many of the same product, even if stock allows it.</p>
                        @error('max_quantity_per_product') <p class="mt-1 text-xs text-[#b91c1c]">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save cart settings</button>
                </form>
            </section>
        </div>

        <div class="space-y-6">
            <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
                <h2 class="text-lg font-semibold text-[#523923]">JazzCash</h2>
                <p class="mt-1 text-xs text-[#8a6c56]">Shown at checkout when a customer selects JazzCash as the payment method.</p>
                <form action="{{ route('settings.options.update') }}" method="POST" class="mt-4 space-y-5" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="jazzcash">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#5a4538]">JazzCash account number <span class="text-[#b91c1c]">*</span></label>
                        <input type="text" name="jazzcash_number" value="{{ old('jazzcash_number', $setting->jazzcash_number) }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                        @error('jazzcash_number') <p class="mt-1 text-xs text-[#b91c1c]">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#5a4538]">Account title <span class="text-[#b91c1c]">*</span></label>
                        <input type="text" name="jazzcash_account_title" value="{{ old('jazzcash_account_title', $setting->jazzcash_account_title) }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                        @error('jazzcash_account_title') <p class="mt-1 text-xs text-[#b91c1c]">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save JazzCash</button>
                </form>
            </section>

            <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
                <h2 class="text-lg font-semibold text-[#523923]">Bank Transfer</h2>
                <p class="mt-1 text-xs text-[#8a6c56]">Shown at checkout when a customer selects Bank Transfer as the payment method.</p>
                <form action="{{ route('settings.options.update') }}" method="POST" class="mt-4 space-y-5" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="bank">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#5a4538]">Bank name <span class="text-[#b91c1c]">*</span></label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $setting->bank_name) }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                        @error('bank_name') <p class="mt-1 text-xs text-[#b91c1c]">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#5a4538]">Account number <span class="text-[#b91c1c]">*</span></label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $setting->bank_account_number) }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                        @error('bank_account_number') <p class="mt-1 text-xs text-[#b91c1c]">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#5a4538]">Account title <span class="text-[#b91c1c]">*</span></label>
                        <input type="text" name="bank_account_title" value="{{ old('bank_account_title', $setting->bank_account_title) }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                        @error('bank_account_title') <p class="mt-1 text-xs text-[#b91c1c]">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save bank details</button>
                </form>
            </section>
        </div>
    </div>
@endsection
