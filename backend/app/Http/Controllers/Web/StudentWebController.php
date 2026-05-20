<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ScheduleSlot;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentWebController extends Controller
{
    public function schedule(): View
    {
        $user = Auth::user();
        $profile = StudentProfile::where('user_id', $user->id)->first();
        $classroom = $profile?->classroom;

        $allSlots = $classroom
            ? ScheduleSlot::where('classroom_id', $classroom->id)
                ->with(['subject', 'teacher', 'classroom.grade'])
                ->orderBy('period_number')
                ->get()
                ->groupBy('day_of_week')
            : collect();

        $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'];
        $selectedDay = request('day', strtolower(now()->format('l')));

        if (!in_array($selectedDay, $days)) {
            $selectedDay = 'sunday';
        }

        $slots = $allSlots->get($selectedDay, collect());

        return view('student.schedule', compact('allSlots', 'days', 'selectedDay', 'slots', 'classroom'));
    }

    public function grades(): View
    {
        $user = Auth::user();
        $profile = StudentProfile::where('user_id', $user->id)->first();
        $classroom = $profile?->classroom;

        return view('student.grades', compact('user', 'classroom'));
    }

    public function attendance(Request $request): View
    {
        $user    = Auth::user();
        $profile = StudentProfile::where('user_id', $user->id)->first();

        $query = Attendance::where('student_user_id', $user->id)
            ->with(['classroom', 'scheduleSlot.subject', 'justification'])
            ->orderByDesc('date');

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $records = $query->paginate(20)->withQueryString();

        // Summary counts
        $summary = Attendance::where('student_user_id', $user->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('student.attendance', compact('user', 'profile', 'records', 'summary'));
    }
}
