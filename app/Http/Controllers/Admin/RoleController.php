<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Rbac\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('roles.manage'), 403);
        $this->ensurePermissionsExist();

        $roles = Role::withCount('permissions')->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('roles.create'), 403);
        $this->ensurePermissionsExist();

        $permissions = $this->permissionsByGroup();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->can('roles.create'), 403);

        $validated = $request->validate([
            'name' => ['required', 'max:255', Rule::unique('roles', 'name')],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::in(PermissionService::PERMISSIONS)],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully!');
    }

    public function edit(Role $role)
    {
        abort_unless(auth()->user()->can('roles.manage'), 403);
        $this->ensurePermissionsExist();

        $permissions = $this->permissionsByGroup();
        $selectedPermissions = $role->permissions->pluck('name')->all();

        return view('admin.roles.edit', compact('role', 'permissions', 'selectedPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        abort_unless($request->user()->can('roles.manage'), 403);

        $validated = $request->validate([
            'name' => ['required', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::in(PermissionService::PERMISSIONS)],
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully!');
    }

    public function destroy(Role $role)
    {
        abort_unless(auth()->user()->can('roles.manage'), 403);

        abort_if($role->name === PermissionService::ROLE_SUPER_ADMIN, 403, 'Super Admin role cannot be deleted.');

        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully!');
    }

    protected function permissionsByGroup(): array
    {
        return collect(PermissionService::PERMISSIONS)
            ->groupBy(fn (string $permission) => str($permission)->before('.')->toString())
            ->map(fn ($permissions) => $permissions->values()->all())
            ->all();
    }

    protected function ensurePermissionsExist(): void
    {
        foreach (PermissionService::PERMISSIONS as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
