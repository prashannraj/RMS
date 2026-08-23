<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminService
{
    public function getDashboardStats(): array
    {
        return app(DashboardService::class)->getStats();
    }

    // ─── Users ───
    public function getPaginatedUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return app(UserRepository::class)->getPaginated($filters, $perPage);
    }

    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone' => $data['phone'] ?? null,
                'is_active' => true,
            ]);

            if (!empty($data['role'])) {
                $user->assignRole($data['role']);
            }

            return $user->load('roles');
        });
    }

    public function updateUser(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user->fresh('roles');
    }

    public function toggleUserActive(User $user): User
    {
        $user->update(['is_active' => !$user->is_active]);

        return $user->fresh();
    }

    // ─── Roles & Permissions ───
    public function getRoles()
    {
        return Role::with('permissions')->get();
    }

    public function createRole(array $data): Role
    {
        $role = Role::create(['name' => $data['name']]);

        if (!empty($data['permissions'])) {
            $role->givePermissionTo($data['permissions']);
        }

        return $role->load('permissions');
    }

    public function updateRole(Role $role, array $data): Role
    {
        if (isset($data['name'])) {
            $role->name = $data['name'];
            $role->save();
        }

        if (array_key_exists('permissions', $data)) {
            $role->syncPermissions($data['permissions'] ?? []);
        }

        return $role->fresh('permissions');
    }

    public function getPermissions()
    {
        return Permission::all();
    }

    public function createPermission(array $data): Permission
    {
        return Permission::create(['name' => $data['name']]);
    }

    // ─── Audit Logs ───
    public function getPaginatedAuditLogs(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = AuditLog::query()->with('user');

        if (!empty($filters['module'])) {
            $query->where('module', $filters['module']);
        }

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['severity'])) {
            $query->where('severity', $filters['severity']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderByDesc('id')->paginate($perPage);
    }
}