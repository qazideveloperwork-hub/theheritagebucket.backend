@extends('layout')
@section('title', 'Users')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Users'],
    ]])

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-semibold text-[#523923]">Users</h1>
        <a href="{{ route('users.create') }}" class="rounded-full bg-[#7C2D12] px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">+ Create User</a>
    </div>

    @include('partials.new-password-banner')

    @php
        $statusTabs = [
            'all' => ['label' => 'All', 'status' => null],
            'active' => ['label' => 'Active', 'status' => 'active'],
            'blocked' => ['label' => 'Blocked', 'status' => 'blocked'],
            'deleted' => ['label' => 'Deleted', 'status' => 'deleted'],
        ];
        $activeStatus = request('status');
        $statusStyles = [
            'active' => 'bg-[#eaf3e5] text-[#3f6a2c]',
            'blocked' => 'bg-[#f1e5d5] text-[#8a6c56]',
            'deleted' => 'bg-[#fdecea] text-[#b91c1c]',
        ];
    @endphp
    <div class="mb-6 flex flex-wrap items-center gap-2 rounded-[28px] border border-[#e6d9c5] bg-white px-5 py-4 text-sm">
        @foreach ($statusTabs as $tab)
            @php $isActive = $activeStatus === $tab['status'] || ($tab['status'] === null && ! $activeStatus); @endphp
            <a href="{{ route('users.index', array_merge(request()->except(['status', 'page']), $tab['status'] ? ['status' => $tab['status']] : [])) }}"
               class="rounded-full px-3 py-1.5 {{ $isActive ? 'bg-[#fdf1e6] font-semibold text-[#7c2d12]' : 'text-[#5a4538] hover:bg-[#f1e5d5]' }}">
                {{ $tab['label'] }}
            </a>
            @if (! $loop->last)
                <span class="text-[#e6d9c5]">|</span>
            @endif
        @endforeach
    </div>

    <form action="{{ route('users.index') }}" method="GET" class="mb-6 flex flex-wrap items-center gap-3 rounded-[28px] border border-[#e6d9c5] bg-white px-5 py-4 text-sm">
        @if ($activeStatus)
            <input type="hidden" name="status" value="{{ $activeStatus }}" />
        @endif
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or email" class="min-w-[220px] flex-1 rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-2.5 text-sm outline-none focus:border-[#7c2d12]" />
        <button type="submit" class="rounded-full bg-[#7C2D12] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#6b2610]">Search</button>
        @if (request('q'))
            <a href="{{ route('users.index', request()->except(['q', 'page'])) }}" class="text-sm text-[#8a6c56] hover:underline">Clear</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-[28px] border border-[#e6d9c5] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#fff7ef] text-xs uppercase tracking-wide text-[#8a6c56]">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Phone</th>
                    <th class="px-5 py-3">City</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Joined</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f3e6d8]">
                @forelse ($customers as $customer)
                    <tr>
                        <td class="px-5 py-3 font-medium text-[#1b1b18]">{{ $customer->name }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $customer->email }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $customer->phone ?: '—' }}</td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $customer->city ?: '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusStyles[$customer->status] ?? $statusStyles['blocked'] }}">
                                {{ ucfirst($customer->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-[#5a4538]">{{ $customer->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('users.show', $customer) }}" class="rounded-full border border-[#d9c9ae] px-4 py-1.5 text-center text-xs font-medium text-[#5a4538] hover:bg-[#f1e5d5]">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-[#8a6c56]">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $customers->links() }}
    </div>
@endsection
