<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(private AdminService $service) {}

    // ─── Dashboard ───
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getDashboardStats(),
        ]);
    }

    // ─── Users ───
    public function usersIndex(Request $request): JsonResponse
    {
        $users = $this->service->getPaginatedUsers(
            $request->only(['search', 'is_active', 'role']),
            (int) $request->query('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function usersStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['nullable', 'string', 'exists:roles,name'],
        ]);

        $user = $this->service->createUser($data);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => $user,
        ], 201);
    }

    public function usersUpdate(int $id, Request $request): JsonResponse
    {
        $user = \App\Models\User::findOrFail($id);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
            'role' => ['nullable', 'string', 'exists:roles,name'],
        ]);

        $updated = $this->service->updateUser($user, $data);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => $updated,
        ]);
    }

    public function usersToggle(int $id): JsonResponse
    {
        $user = \App\Models\User::findOrFail($id);
        $updated = $this->service->toggleUserActive($user);

        return response()->json([
            'success' => true,
            'message' => $updated->is_active ? 'User activated.' : 'User deactivated.',
            'data' => $updated,
        ]);
    }

    // ─── Roles ───
    public function rolesIndex(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getRoles(),
        ]);
    }

    public function rolesStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = $this->service->createRole($data);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully.',
            'data' => $role,
        ], 201);
    }

    public function rolesUpdate(int $id, Request $request): JsonResponse
    {
        $role = \Spatie\Permission\Models\Role::findOrFail($id);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:100', 'unique:roles,name,' . $id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $updated = $this->service->updateRole($role, $data);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully.',
            'data' => $updated,
        ]);
    }

    // ─── Permissions ───
    public function permissionsIndex(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getPermissions(),
        ]);
    }

    public function permissionsStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:permissions,name'],
        ]);

        $permission = $this->service->createPermission($data);

        return response()->json([
            'success' => true,
            'message' => 'Permission created successfully.',
            'data' => $permission,
        ], 201);
    }

    // ─── Audit Logs ───
    public function auditLogs(Request $request): JsonResponse
    {
        $logs = $this->service->getPaginatedAuditLogs(
            $request->only(['module', 'action', 'severity', 'date_from', 'date_to']),
            (int) $request->query('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ],
        ]);
    }
}