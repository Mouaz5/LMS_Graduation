<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreJustificationWebRequest;
use App\Models\AbsenceJustification;
use App\Models\Attendance;
use App\Models\BehavioralNote;
use App\Models\ExamType;
use App\Models\GradeSummary;
use App\Models\ScheduleSlot;
use App\Models\Semester;
use App\Models\StudentGrade;
use App\Models\StudentProfile;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ParentWebController extends Controller
{
    public function children(): View
    {
        $user = Auth::user();
        $children = $user->children()->with('studentProfile.classroom.grade')->get();

        return view('parent.children', compact('user', 'children'));
    }

    public function childSchedule(User $child): View
    {
        $user = Auth::user();

        // Verify this child belongs to the parent
        if (!$user->children()->where('student_user_id', $child->id)->exists()) {
            abort(403, __('This student is not linked to your account.'));
        }

        $profile = StudentProfile::where('user_id', $child->id)->first();
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

        return view('parent.child-schedule', compact('allSlots', 'days', 'selectedDay', 'slots', 'classroom', 'child'));
    }

    public function results(Request $request): View
    {
        $parent   = Auth::user();
        $children = $parent->children()->with('studentProfile.classroom.grade')->get();

        $selectedChildId = $request->input('child_id', $children->first()?->id);
        $selectedChild   = $children->firstWhere('id', $selectedChildId);

        $semesters = Semester::with('academicYear')->orderByDesc('id')->get();
        $selectedSemesterId = $request->integer('semester_id') ?: $semesters->first()?->id;

        $summaries  = collect();
        $grades     = collect();
        $examTypes  = collect();

        if ($selectedChild) {
            abort_unless($parent->children()->where('student_user_id', $selectedChild->id)->exists(), 403);

            $summaries = GradeSummary::where('student_user_id', $selectedChild->id)
                ->where('semester_id', $selectedSemesterId)
                ->with('subject')
                ->get();

            $grades = StudentGrade::where('student_user_id', $selectedChild->id)
                ->where('semester_id', $selectedSemesterId)
                ->with(['subject', 'examType'])
                ->get()
                ->groupBy('subject_id');

            $examTypes = ExamType::where('semester_id', $selectedSemesterId)->orderBy('id')->get();
        }

        return view('parent.results', compact(
            'parent', 'children', 'selectedChild', 'semesters',
            'selectedSemesterId', 'summaries', 'grades', 'examTypes'
        ));
    }

    public function grades(): View
    {
        $user = Auth::user();
        $children = $user->children()->with('studentProfile.classroom.grade')->get();

        return view('parent.grades', compact('user', 'children'));
    }

    public function downloadReportCard(Request $request, User $child): \Illuminate\Http\Response
    {
        $parent = Auth::user();
        abort_unless($parent->children()->where('student_user_id', $child->id)->exists(), 403);

        $semesterId = $request->integer('semester_id');
        $semester   = $semesterId ? Semester::with('academicYear')->find($semesterId) : null;

        $student   = $child->load('studentProfile.classroom.grade');
        $summaries = GradeSummary::where('student_user_id', $child->id)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->with('subject')
            ->get();

        $grades = StudentGrade::where('student_user_id', $child->id)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->with(['subject', 'examType'])
            ->get()
            ->groupBy('subject_id');

        $examTypes = $semesterId
            ? ExamType::where('semester_id', $semesterId)->orderBy('id')->get()
            : collect();

        $pdf = Pdf::loadView('pdf.report_card', compact('student', 'semester', 'summaries', 'grades', 'examTypes'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("report_card_{$child->name}_{$semesterId}.pdf");
    }

    public function attendance(Request $request): View
    {
        $parent   = Auth::user();
        $children = $parent->children()->with('studentProfile.classroom.grade')->get();

        $selectedChildId = $request->input('child_id', $children->first()?->id);
        $selectedChild   = $children->firstWhere('id', $selectedChildId);

        $records = collect();
        if ($selectedChild) {
            $query = Attendance::where('student_user_id', $selectedChild->id)
                ->with(['classroom', 'scheduleSlot.subject', 'justification'])
                ->orderByDesc('date');

            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
            }

            $records = $query->paginate(20)->withQueryString();
        }

        return view('parent.attendance', compact('parent', 'children', 'selectedChild', 'records'));
    }

    public function behavioralNotes(Request $request): View
    {
        $parent   = Auth::user();
        $children = $parent->children()->get();

        $selectedChildId = $request->input('child_id', $children->first()?->id);
        $selectedChild   = $children->firstWhere('id', $selectedChildId);

        $notes = collect();
        if ($selectedChild) {
            abort_unless(
                $parent->children()->where('student_user_id', $selectedChild->id)->exists(),
                403
            );

            $notes = BehavioralNote::where('student_user_id', $selectedChild->id)
                ->with('teacher')
                ->orderByDesc('date')
                ->paginate(20);
        }

        return view('parent.behavioral-notes', compact('parent', 'children', 'selectedChild', 'notes'));
    }

    public function storeJustification(StoreJustificationWebRequest $request, Attendance $attendance): RedirectResponse
    {
        $parent = Auth::user();

        abort_unless(
            $parent->children()->where('student_user_id', $attendance->student_user_id)->exists(),
            403,
            __('You are not a parent of this student.')
        );

        abort_if(
            $attendance->justification()->exists(),
            422,
            __('A justification already exists for this absence.')
        );

        $documentUrl = null;
        if ($request->hasFile('document')) {
            $path        = $request->file('document')->store('justifications', 'public');
            $documentUrl = Storage::url($path);
        }

        AbsenceJustification::create([
            'attendance_id' => $attendance->id,
            'reason'        => $request->reason,
            'submitted_by'  => $parent->id,
            'document_url'  => $documentUrl,
            'status'        => 'pending',
        ]);

        return redirect()->route('parent.attendance', ['child_id' => $attendance->student_user_id])
            ->with('success', __('Justification submitted successfully.'));
    }
}
