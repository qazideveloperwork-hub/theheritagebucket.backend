@extends('layout')
@section('title', 'Info Library')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Settings', 'url' => route('settings.shipping')],
        ['label' => 'Info Library'],
    ]])

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

    <div class="grid gap-6 lg:grid-cols-[420px_1fr] lg:items-start">
        <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
            <h2 class="text-lg font-semibold text-[#523923]">Add Info</h2>
            <form action="{{ route('infos.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#5a4538]">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" placeholder="Size guide" />
                </div>
                <button type="submit" class="w-full rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Submit</button>
            </form>
        </section>

        <section>
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-[#523923]">Info Library</h1>
                <span class="text-sm text-[#8a6c56]">All ({{ $infos->total() }})</span>
            </div>

            <div class="overflow-hidden rounded-[28px] border border-[#e6d9c5] bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#fff7ef] text-xs uppercase tracking-wide text-[#8a6c56]">
                        <tr>
                            <th class="px-5 py-3">Title</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f3e6d8]">
                        @forelse ($infos as $info)
                            <tr>
                                <td class="px-5 py-3 font-medium">
                                    <a href="{{ route('infos.edit', $info) }}" class="text-[#7c2d12] hover:underline">{{ $info->title }}</a>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <form action="{{ route('infos.destroy', $info) }}" method="POST" onsubmit="return confirm('Delete info {{ addslashes($info->title) }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-full bg-[#e46a5c] px-5 py-1.5 text-xs font-medium text-white hover:bg-[#d65847]">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-10 text-center text-sm text-[#8a6c56]">No info entries yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $infos->links() }}
            </div>
        </section>
    </div>
@endsection
