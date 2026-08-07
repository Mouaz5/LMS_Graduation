<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\TeacherAttendanceStoreRequest;
use App\Models\AbsenceJustification;
use App\Services\Attendance\AbsenceJustificationService;
use App\Services\Attendance\TeacherAttendanceQueryService;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeacherAttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $service,
        private TeacherAttendanceQueryService $queries,
        private AbsenceJustificationService $justifications,
    ) {}

    /**
     * GET /teacher/attendance
     * Shows date picker + classroom selector. If both selected, loads student roster
     * with any pre-existing attendance pre-populated.
     */
    public function index(Request $request): View
    {
        $data = $this->queries->formData(
            Auth::user(),
            $request->input('classroom_id'),
            $request->input('date', now()->toDateString()),
        );

        return view('teacher.attendance', $data);
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
                'status' => $status,
            ];
        }

        $this->service->recordBulk(
            teacher: Auth::user(),
            classroomId: (int) $request->classroom_id,
            date: $request->date,
            entries: $entries,
            scheduleSlotId: $request->schedule_slot_id ? (int) $request->schedule_slot_id : null,
        );

        return redirect()
            ->route('teacher.attendance', [
                'date' => $request->date,
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
        $justifications = $this->queries->pendingJustifications(Auth::user());

        return view('teacher.justifications', compact('justifications'));
    }

    /**
     * POST /teacher/attendance/justifications/{justification}/approve
     */
    public function approveJustification(AbsenceJustification $justification): RedirectResponse
    {
        $this->justifications->approve(Auth::user(), $justification);

        return redirect()->route('teacher.justifications')
            ->with('success', __('Justification approved and attendance marked as excused.'));
    }

    /**
     * POST /teacher/attendance/justifications/{justification}/reject
     */
    public function rejectJustification(AbsenceJustification $justification): RedirectResponse
    {
        $this->justifications->reject(Auth::user(), $justification);

        return redirect()->route('teacher.justifications')
            ->with('success', __('Justification rejected.'));
    }
}
