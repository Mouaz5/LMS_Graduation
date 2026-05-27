<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateRoleRequest;
use App\Http\Requests\User\UpdateStatusRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::select('id', 'name', 'email', 'role', 'phone', 'is_active', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($users);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json($user->only('id', 'name', 'email', 'role', 'phone', 'is_active', 'created_at'));
    }

    public function updateRole(UpdateRoleRequest $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['role' => $request->role]);

        return response()->json(['message' => 'Role updated.', 'user' => $user->only('id', 'name', 'email', 'role')]);
    }

    public function updateStatus(UpdateStatusRequest $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => $request->is_active]);

        return response()->json(['message' => 'Status updated.', 'is_active' => $user->is_active]);
    }
}
