<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\TeacherAttendanceStoreRequest;
use App\Models\AbsenceJustification;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeacherAttendanceController extends Controller
{
    public function __construct(private AttendanceService $service) {}

    /**
     * GET /teacher/attendance
     * Shows date picker + classroom selector. If both selected, loads student roster
     * with any pre-existing attendance pre-populated.
     */
    public function index(Request $request): View
    {
        $teacher    = Auth::user();
        $classrooms = $this->getTeacherClassrooms($teacher);

        $selectedDate        = $request->input('date', now()->toDateString());
        $selectedClassroomId = $request->input('classroom_id');
        $students            = collect();
        $existingAttendance  = collect();

        if ($selectedClassroomId) {
            $students = $this->service->getClassroomStudents((int) $selectedClassroomId);

            $existingAttendance = Attendance::where('classroom_id', $selectedClassroomId)
                ->whereDate('date', $selectedDate)
                ->get()
                ->keyBy('student_user_id');
        }

        return view('teacher.attendance', compact(
            'classrooms', 'selectedDate', 'selectedClassroomId',
            'students', 'existingAttendance'
        ));
    }

    /**
     * POST /teacher/attendance
     * Form payload: classroom_id, date, schedule_slot_id?, statuses[{student_id}] = status
     */
    public function store(TeacherAttendanceStoreRequest $request): RedirectResponse
    {
        $entries = [];
        foreach ($request->statuses as $studentId => $status) {
            $entries[] = [
                'student_id' => (int) $studentId,
                'status'     => $status,
            ];
        }

        $this->service->recordBulk(
            teacher:        Auth::user(),
            classroomId:    (int) $request->classroom_id,
            date:           $request->date,
            entries:        $entries,
            scheduleSlotId: $request->schedule_slot_id ? (int) $request->schedule_slot_id : null,
        );

        return redirect()
            ->route('teacher.attendance', [
                'date'         => $request->date,
                'classroom_id' => $request->classroom_id,
            ])
            ->with('success', __('Attendance recorded successfully.'));
    }

    /**
     * GET /teacher/attendance/justifications
     * Lists all pending justifications for classrooms the teacher teaches.
     */
    public function justifications(): View
    {
        $teacher      = Auth::user();
        $classroomIds = $teacher->teacherAssignments()->pluck('classroom_id');

        $justifications = AbsenceJustification::with([
                'attendance.student',
                'attendance.classroom',
                'submittedBy',
            ])
            ->whereHas('attendance', fn ($q) => $q->whereIn('classroom_id', $classroomIds))
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('teacher.justifications', compact('justifications'));
    }

    /**
     * POST /teacher/attendance/justifications/{justification}/approve
     */
    public function approveJustification(AbsenceJustification $justification): RedirectResponse
    {
        $this->service->assertTeacherCanRecord(
            Auth::user(),
            $justification->attendance->classroom_id,
            null
        );

        $this->service->approveJustification($justification);

        return redirect()->route('teacher.justifications')
            ->with('success', __('Justification approved and attendance marked as excused.'));
    }

    /**
     * POST /teacher/attendance/justifications/{justification}/reject
     */
    public function rejectJustification(AbsenceJustification $justification): RedirectResponse
    {
        $this->service->assertTeacherCanRecord(
            Auth::user(),
            $justification->attendance->classroom_id,
            null
        );

        $this->service->rejectJustification($justification);

        return redirect()->route('teacher.justifications')
            ->with('success', __('Justification rejected.'));
    }

    private function getTeacherClassrooms($teacher)
    {
        return Classroom::whereHas('teacherAssignments', fn ($q) => $q->where('teacher_user_id', $teacher->id))
            ->with('grade')
            ->get();
    }
}
