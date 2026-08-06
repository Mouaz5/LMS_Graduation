<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = ['admin', 'teacher', 'student', 'parent'];
        $permissions = Permission::with('roles')->get();

        $result = [];
        foreach ($roles as $role) {
            $result[$role] = $permissions
                ->filter(fn ($p) => $p->roles->contains('role', $role))
                ->pluck('slug')
                ->values();
        }

        return response()->json($result);
    }
}
