<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Rbac\PermissionService;
use App\Support\AdminTableSort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        
        $allowedSorts = ['name', 'username', 'email', 'created_at', 'updated_at'];
        [$sortBy, $sortDirection] = AdminTableSort::resolve($request, $allowedSorts);

        $users = AdminTableSort::apply(User::with('roles'), $allowedSorts, $sortBy, $sortDirection)
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'sortBy', 'sortDirection'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        
        $roles = $this->assignableRoles();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        
        $roles = $this->assignableRoles();

        $validated = $request->validate([
            'name' => 'required|max:255',
            'username' => 'required|unique:users|max:50|regex:/^[a-zA-Z0-9_-]+$/',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:10|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
            'role' => 'required|in:' . $roles->pluck('name')->implode(','),
            'status' => 'required|in:active,inactive,banned',
            'avatar' => 'nullable|url|max:255',
            'bio' => 'nullable|string|max:2000',
        ], [
            'username.regex' => 'Username can only contain letters, numbers, hyphens and underscores.',
            'password.regex' => 'Password must contain uppercase, lowercase, and numbers.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
            'avatar' => $validated['avatar'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        $roles = $this->assignableRoles();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        
        $roles = $this->assignableRoles();

        $validated = $request->validate([
            'name' => 'required|max:255',
            'username' => 'required|max:50|regex:/^[a-zA-Z0-9_-]+$/|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:' . $roles->pluck('name')->implode(','),
            'status' => 'required|in:active,inactive,banned',
            'avatar' => 'nullable|url|max:255',
            'bio' => 'nullable|string|max:2000',
            'password' => 'nullable|min:10|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
        ], [
            'username.regex' => 'Username can only contain letters, numbers, hyphens and underscores.',
            'password.regex' => 'Password must contain uppercase, lowercase, and numbers.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'status' => $validated['status'],
            'avatar' => $validated['avatar'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]);
        
        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }
        
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        // Prevent deletion of last Super Admin
        if ($user->hasRole('Super Admin') && User::role('Super Admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the last Super Admin user.');
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    private function assignableRoles()
    {
        $roleNames = PermissionService::roles();

        if (! auth()->user()?->can('roles.manage')) {
            $roleNames = array_values(array_diff($roleNames, ['Super Admin']));
        }

        return Role::whereIn('name', $roleNames)->orderBy('name')->get();
    }
}
