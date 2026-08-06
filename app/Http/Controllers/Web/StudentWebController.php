<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ExamType;
use App\Models\GradeSummary;
use App\Models\Semester;
use App\Models\ScheduleSlot;
use App\Models\StudentGrade;
use App\Models\StudentProfile;
use Barryvdh\DomPDF\Facade\Pdf;
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

    public function results(Request $request): View
    {
        $user = Auth::user();

        $semesters = Semester::with('academicYear')->orderByDesc('id')->get();
        $selectedSemesterId = $request->integer('semester_id') ?: $semesters->first()?->id;

        $summaries = GradeSummary::where('student_user_id', $user->id)
            ->where('semester_id', $selectedSemesterId)
            ->with('subject')
            ->get();

        $grades = StudentGrade::where('student_user_id', $user->id)
            ->where('semester_id', $selectedSemesterId)
            ->with(['subject', 'examType'])
            ->get()
            ->groupBy('subject_id');

        $examTypes = $selectedSemesterId
            ? ExamType::where('semester_id', $selectedSemesterId)->orderBy('id')->get()
            : collect();

        return view('student.results', compact('user', 'semesters', 'selectedSemesterId', 'summaries', 'grades', 'examTypes'));
    }

    public function downloadReportCard(Request $request): \Illuminate\Http\Response
    {
        $user       = Auth::user();
        $semesterId = $request->integer('semester_id');
        $semester   = $semesterId ? Semester::with('academicYear')->find($semesterId) : null;
        $student    = $user->load('studentProfile.classroom.grade');

        $summaries = GradeSummary::where('student_user_id', $user->id)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->with('subject')
            ->get();

        $grades = StudentGrade::where('student_user_id', $user->id)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->with(['subject', 'examType'])
            ->get()
            ->groupBy('subject_id');

        $examTypes = $semesterId
            ? ExamType::where('semester_id', $semesterId)->orderBy('id')->get()
            : collect();

        $pdf = Pdf::loadView('pdf.report_card', compact('student', 'semester', 'summaries', 'grades', 'examTypes'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("report_card_{$user->name}_{$semesterId}.pdf");
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
