<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $role = $user->role;

        return match ($role) {
            'admin' => view('dashboards.admin', ['user' => $user, 'stats' => $this->adminStats()]),
            'teacher' => view('dashboards.teacher', ['user' => $user]),
            'student' => view('dashboards.student', ['user' => $user]),
            'parent' => view('dashboards.parent', ['user' => $user]),
            default => view('dashboards.admin', ['user' => $user, 'stats' => $this->adminStats()]),
        };
    }

    private function adminStats(): array
    {
        return [
            'total_users' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'students' => User::where('role', 'student')->count(),
            'parents' => User::where('role', 'parent')->count(),
            'active_users' => User::where('is_active', true)->count(),
        ];
    }

    public function impersonate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['role' => 'required|in:admin,teacher,student,parent']);
        session(['impersonate_role' => $request->role]);
        return redirect()->route('dashboard');
    }

    public function stopImpersonate(): \Illuminate\Http\RedirectResponse
    {
        session()->forget('impersonate_role');
        return redirect()->route('dashboard');
    }
}
