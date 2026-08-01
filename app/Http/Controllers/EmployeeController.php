<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    private const ROLES = ['admin', 'employee'];

    public function index(Request $request): View
    {
        $employees = User::query()
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

        return view('employees.index', ['employees' => $employees]);
    }

    public function create(): View
    {
        return view('employees.create', ['roles' => self::ROLES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admin_users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:'.implode(',', self::ROLES)],
        ]);

        User::create($data);

        return redirect()->route('employees.index')->with('success', 'Employee created.');
    }

    public function show(User $employee): View
    {
        return view('employees.show', ['employee' => $employee]);
    }

    public function updateStatus(Request $request, User $employee): RedirectResponse
    {
        abort_if($employee->id === $request->user()->id, 403, 'You cannot block your own account.');

        $employee->update(['status' => $employee->status === 'blocked' ? 'active' : 'blocked']);

        return back()->with('success', $employee->status === 'blocked' ? 'Employee blocked.' : 'Employee activated.');
    }

    public function destroy(Request $request, User $employee): RedirectResponse
    {
        abort_if($employee->id === $request->user()->id, 403, 'You cannot delete your own account.');

        $employee->update(['status' => 'deleted']);

        return redirect()->route('employees.index')->with('success', "Employee \"{$employee->name}\" was deleted.");
    }

    public function resetPassword(User $employee): RedirectResponse
    {
        $newPassword = Str::password(12);

        $employee->update(['password' => $newPassword]);

        return back()->with([
            'success' => 'Password reset for '.$employee->name.'.',
            'new_password_for' => $employee->id,
            'new_password' => $newPassword,
        ]);
    }
}
