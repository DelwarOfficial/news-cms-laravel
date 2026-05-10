<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Rbac\PermissionService;
use App\Support\AdminTableSort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        $this->ensureMemberRolesExist();
        $roles = $this->memberRoles();

        $allowedSorts = ['name', 'username', 'email', 'status', 'created_at', 'updated_at'];
        [$sortBy, $sortDirection] = AdminTableSort::resolve($request, $allowedSorts, 'created_at', 'desc');

        $role = $request->query('role', '');
        if ($role && ! in_array($role, $roles, true)) {
            $role = '';
        }

        $query = User::with('roles')
            ->role($role ?: $roles)
            ->when($request->query('search'), function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('username', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            });

        $members = AdminTableSort::apply($query, $allowedSorts, $sortBy, $sortDirection)
            ->paginate(20)
            ->withQueryString();

        return view('admin.members.index', compact('members', 'roles', 'role', 'sortBy', 'sortDirection'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $this->ensureMemberRolesExist();

        $roles = $this->memberRoles();

        return view('admin.members.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        $this->ensureMemberRolesExist();

        $validated = $request->validate([
            'name' => 'required|max:255',
            'username' => 'required|unique:users|max:50|regex:/^[a-zA-Z0-9_-]+$/',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:' . implode(',', $this->memberRoles()),
            'status' => 'required|in:active,inactive,banned',
            'avatar' => 'nullable|url|max:255',
            'bio' => 'nullable|max:1000',
        ], [
            'username.regex' => 'Username can only contain letters, numbers, hyphens and underscores.',
        ]);

        $member = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
            'avatar' => $validated['avatar'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]);

        $member->assignRole($validated['role']);

        return redirect()->route('admin.members.index')->with('success', 'Member created successfully!');
    }

    public function edit(User $member)
    {
        $this->authorize('update', $member);
        $this->ensureMemberRolesExist();

        abort_unless($member->hasRole($this->memberRoles()), 404);

        $roles = $this->memberRoles();

        return view('admin.members.edit', compact('member', 'roles'));
    }

    public function update(Request $request, User $member)
    {
        $this->authorize('update', $member);
        $this->ensureMemberRolesExist();
        abort_unless($member->hasRole($this->memberRoles()), 404);

        $validated = $request->validate([
            'name' => 'required|max:255',
            'username' => 'required|max:50|regex:/^[a-zA-Z0-9_-]+$/|unique:users,username,' . $member->id,
            'email' => 'required|email|unique:users,email,' . $member->id,
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|in:' . implode(',', $this->memberRoles()),
            'status' => 'required|in:active,inactive,banned',
            'avatar' => 'nullable|url|max:255',
            'bio' => 'nullable|max:1000',
        ], [
            'username.regex' => 'Username can only contain letters, numbers, hyphens and underscores.',
        ]);

        $member->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'status' => $validated['status'],
            'avatar' => $validated['avatar'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $member->update(['password' => Hash::make($validated['password'])]);
        }

        $member->syncRoles([$validated['role']]);

        return redirect()->route('admin.members.index')->with('success', 'Member updated successfully!');
    }

    public function destroy(User $member)
    {
        $this->authorize('delete', $member);
        $this->ensureMemberRolesExist();
        abort_unless($member->hasRole($this->memberRoles()), 404);

        $member->delete();

        return redirect()->route('admin.members.index')->with('success', 'Member deleted successfully!');
    }

    protected function memberRoles(): array
    {
        return PermissionService::editorialRoles();
    }

    protected function ensureMemberRolesExist(): void
    {
        foreach (PermissionService::PERMISSIONS as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        foreach ($this->memberRoles() as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ])->syncPermissions(PermissionService::ROLE_PERMISSIONS[$roleName]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
