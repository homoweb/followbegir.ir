<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * All users with optional search.
     */
    public function index(Request $request): Response
    {
        $users = User::query()
            ->when($request->string('q')->toString(), function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->with('roles:id,name')
            ->latest()
            ->paginate(config('followbegir.order.per_page'))
            ->through(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
                'roles' => $user->roles->pluck('name'),
                'created_at' => $user->created_at->toIso8601String(),
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => ['q' => $request->string('q')->toString()],
        ]);
    }

    /**
     * The user creation form.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create');
    }

    /**
     * Store a new user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'is_active' => ['required', 'boolean'],
            'role' => ['required', 'string', Rule::in(['admin', 'user'])],
        ], [
            'email.unique' => 'این ایمیل قبلاً ثبت شده است.',
            'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
        ]);

        $user = User::query()->create([
            ...collect($validated)->except('role')->all(),
            'password' => $validated['password'],
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')
            ->with('success', 'کاربر جدید ایجاد شد.');
    }

    /**
     * The user edit form.
     */
    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
                'role' => $user->hasRole('admin') ? 'admin' : 'user',
            ],
        ]);
    }

    /**
     * Update a user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['required', 'boolean'],
            'role' => ['required', 'string', Rule::in(['admin', 'user'])],
        ], [
            'email.unique' => 'این ایمیل قبلاً ثبت شده است.',
            'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
        ]);

        $user->fill(collect($validated)->except('role', 'password')->all());

        if (($validated['password'] ?? null) !== null && $validated['password'] !== '') {
            $user->password = $validated['password'];
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', 'اطلاعات کاربر به‌روزرسانی شد.');
    }

    /**
     * Deactivate / activate a user.
     */
    public function toggle(User $user): RedirectResponse
    {
        if ($user->getKey() === auth()->id()) {
            return back()->with('error', 'نمی‌توانید حساب خودتان را غیرفعال کنید.');
        }

        $user->forceFill(['is_active' => ! $user->is_active])->save();

        return back()->with('success', 'وضعیت کاربر تغییر کرد.');
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->getKey() === auth()->id()) {
            return back()->with('error', 'نمی‌توانید حساب خودتان را حذف کنید.');
        }

        $user->delete();

        return back()->with('success', 'کاربر حذف شد.');
    }
}
