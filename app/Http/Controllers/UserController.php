<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q');
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->input('status')),
                fn ($query) => $query->where('status', '!=', 'deleted')
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('users.index', ['customers' => $customers]);
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8'],
            'street' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        Customer::create($data);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function show(Customer $user): View
    {
        $user->load(['orders' => fn ($query) => $query->latest()]);

        return view('users.show', ['customer' => $user]);
    }

    public function updateStatus(Customer $user): RedirectResponse
    {
        $user->update(['status' => $user->status === 'blocked' ? 'active' : 'blocked']);

        return back()->with('success', $user->status === 'blocked' ? 'User blocked.' : 'User activated.');
    }

    public function destroy(Customer $user): RedirectResponse
    {
        $user->update(['status' => 'deleted']);

        return redirect()->route('users.index')->with('success', "User \"{$user->name}\" was deleted.");
    }

    public function resetPassword(Customer $user): RedirectResponse
    {
        $newPassword = Str::password(12);

        $user->update(['password' => $newPassword]);

        return back()->with([
            'success' => 'Password reset for '.$user->name.'.',
            'new_password_for' => $user->id,
            'new_password' => $newPassword,
        ]);
    }
}
