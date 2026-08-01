@extends('layout')
@section('title', 'Import Products')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Products', 'url' => route('products.list')],
        ['label' => 'Import'],
    ]])
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-[#523923]">Import Products</h1>
        <p class="mt-1 text-sm text-[#8a6c56]">Bulk-create simple products from an Excel file. Variable (sized) products and images aren't supported here — add those manually after import. Imported products are always created as Draft (regardless of the Status column) since they have no image yet; publish them once you've added photos.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-[#c7d9c0] bg-[#eef6ea] px-4 py-3 text-sm text-[#3f6a2c]">
            {{ session('success') }}
        </div>
    @endif

    @if (session('import_errors'))
        <div class="mb-6 rounded-2xl border border-[#e3b3a3] bg-[#fdecea] px-4 py-3 text-sm text-[#7c2d12]">
            <p class="font-semibold">
                @if (session('import_success_count', 0) > 0)
                    Imported {{ session('import_success_count') }} product(s), but some rows failed:
                @else
                    Import failed — no products were created:
                @endif
            </p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach (session('import_errors') as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-[#e3b3a3] bg-[#fdecea] px-4 py-3 text-sm text-[#7c2d12]">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <h2 class="text-lg font-semibold text-[#523923]">1. Download the template</h2>
            <p class="mt-2 text-sm text-[#8a6c56]">Start from the blank template so your columns match exactly. Category and Supplier must match existing names in the admin.</p>
            <a href="{{ route('products.import.template') }}" class="mt-4 inline-block rounded-full border border-[#d9c9ae] px-5 py-3 text-sm font-medium text-[#5a4538] hover:bg-[#f1e5d5]">Download template (.xlsx)</a>
        </section>

        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <h2 class="text-lg font-semibold text-[#523923]">2. Upload your file</h2>
            <form action="{{ route('products.import.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
                <button type="submit" class="rounded-full bg-[#7C2D12] px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Upload &amp; import</button>
            </form>
        </section>
    </div>
@endsection
