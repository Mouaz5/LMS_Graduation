<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\ScheduleSlot;
use App\Models\StudentProfile;
use App\Models\TeacherSubjectClassroom;
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
            'teacher' => $this->teacherDashboard($user),
            'student' => $this->studentDashboard($user),
            'parent' => $this->parentDashboard($user),
            default => view('dashboards.admin', ['user' => $user, 'stats' => $this->adminStats()]),
        };
    }

    private function teacherDashboard(User $user): View
    {
        $today = strtolower(now()->format('l'));

        $classrooms = Classroom::whereHas('teacherAssignments', fn ($q) => $q->where('teacher_user_id', $user->id))
            ->with('grade')
            ->withCount('studentProfiles')
            ->get();

        $assignments = TeacherSubjectClassroom::where('teacher_user_id', $user->id)
            ->with(['subject', 'classroom.grade'])
            ->get();

        $todaySlots = ScheduleSlot::where('teacher_user_id', $user->id)
            ->where('day_of_week', $today)
            ->with(['subject', 'classroom'])
            ->orderBy('period_number')
            ->get();

        return view('dashboards.teacher', compact('user', 'classrooms', 'assignments', 'todaySlots'));
    }

    private function studentDashboard(User $user): View
    {
        $profile = StudentProfile::where('user_id', $user->id)->first();
        $classroom = $profile?->classroom;
        $today = strtolower(now()->format('l'));

        $todaySlots = $classroom
            ? ScheduleSlot::where('classroom_id', $classroom->id)
                ->where('day_of_week', $today)
                ->with(['subject', 'teacher'])
                ->orderBy('period_number')
                ->get()
            : collect();

        return view('dashboards.student', compact('user', 'classroom', 'todaySlots'));
    }

    private function parentDashboard(User $user): View
    {
        $children = $user->children()->with('studentProfile.classroom.grade')->get();

        return view('dashboards.parent', compact('user', 'children'));
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
        abort_unless($request->user()->role === 'admin', 403);

        $request->validate(['role' => 'required|in:admin,teacher,student,parent']);
        session(['impersonate_role' => $request->role]);
        return redirect()->route('dashboard');
    }

    public function stopImpersonate(Request $request): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);

        session()->forget('impersonate_role');
        return redirect()->route('dashboard');
    }
}
