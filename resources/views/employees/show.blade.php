@extends('layout')
@section('title', 'Employee: '.$employee->name)
@section('content')
    @php
        $isSelf = $employee->id === auth()->id();
        $statusStyles = [
            'active' => 'bg-[#eaf3e5] text-[#3f6a2c]',
            'blocked' => 'bg-[#f1e5d5] text-[#8a6c56]',
            'deleted' => 'bg-[#fdecea] text-[#b91c1c]',
        ];
    @endphp

    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Employees', 'url' => route('employees.index')],
        ['label' => $employee->name],
    ]])

    @include('partials.new-password-banner')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-semibold text-[#523923]">{{ $employee->name }} @if ($isSelf) <span class="text-sm font-normal text-[#8a6c56]">(you)</span> @endif</h1>
        <div class="flex gap-3">
            <form action="{{ route('employees.reset-password', $employee) }}" method="POST" data-confirm="Reset password for {{ addslashes($employee->name) }}? Their current password will stop working.">
                @csrf
                <button type="submit" class="rounded-full border border-[#d9c9ae] px-5 py-2.5 text-sm font-medium text-[#5a4538] hover:bg-[#f1e5d5]">Reset Password</button>
            </form>
            @unless ($isSelf)
                <form action="{{ route('employees.status', $employee) }}" method="POST" data-confirm="{{ $employee->status === 'blocked' ? 'Activate' : 'Block' }} {{ addslashes($employee->name) }}?">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-full border border-[#d9c9ae] px-5 py-2.5 text-sm font-medium text-[#5a4538] hover:bg-[#f1e5d5]">
                        {{ $employee->status === 'blocked' ? 'Activate' : 'Block' }}
                    </button>
                </form>
                <form action="{{ route('employees.destroy', $employee) }}" method="POST" data-confirm="Delete {{ addslashes($employee->name) }}? They will be marked as Deleted and hidden from this list.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-full border border-[#e3b3a3] px-5 py-2.5 text-sm font-medium text-[#7c2d12] hover:bg-[#fdecea]">Delete</button>
                </form>
            @endunless
        </div>
    </div>

    <section class="rounded-[28px] border border-[#e6d9c5] bg-white p-6">
        <h2 class="text-lg font-semibold text-[#523923]">Profile</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 text-sm">
            <div><span class="block text-xs uppercase text-[#8a6c56]">Email</span>{{ $employee->email }}</div>
            <div><span class="block text-xs uppercase text-[#8a6c56]">Role</span>{{ ucfirst($employee->role) }}</div>
            <div><span class="block text-xs uppercase text-[#8a6c56]">Joined</span>{{ $employee->created_at->format('d M Y, H:i') }}</div>
            <div>
                <span class="block text-xs uppercase text-[#8a6c56]">Status</span>
                <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusStyles[$employee->status] ?? $statusStyles['blocked'] }}">
                    {{ ucfirst($employee->status) }}
                </span>
            </div>
        </div>
    </section>
@endsection
