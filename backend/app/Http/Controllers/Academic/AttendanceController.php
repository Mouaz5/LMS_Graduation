<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\TeacherSubjectClassroom;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $service) {}

    /**
     * POST /api/v1/attendance/bulk
     *
     * Body: { classroom_id, date, schedule_slot_id?, entries: [{student_id, status}] }
     */
    public function bulkStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'classroom_id'         => 'required|exists:classrooms,id',
            'date'                 => 'required|date|date_format:Y-m-d',
            'schedule_slot_id'     => 'nullable|exists:schedule_slots,id',
            'entries'              => 'required|array|min:1',
            'entries.*.student_id' => 'required|exists:users,id',
            'entries.*.status'     => 'required|in:present,absent,late,excused',
        ]);

        $records = $this->service->recordBulk(
            teacher:        $request->user(),
            classroomId:    $validated['classroom_id'],
            date:           $validated['date'],
            entries:        $validated['entries'],
            scheduleSlotId: $validated['schedule_slot_id'] ?? null,
        );

        return response()->json($records, 201);
    }

    /**
     * GET /api/v1/attendance?student_id=X&date_from=Y&date_to=Z
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'date_from'  => 'nullable|date',
            'date_to'    => 'nullable|date|after_or_equal:date_from',
        ]);

        $user      = $request->user();
        $studentId = $request->integer('student_id');

        match ($user->role) {
            'student' => abort_if($user->id !== $studentId, 403, 'Access denied.'),
            'parent'  => abort_unless(
                $user->children()->where('student_user_id', $studentId)->exists(),
                403, 'This student is not your child.'
            ),
            'teacher' => abort_unless(
                TeacherSubjectClassroom::where('teacher_user_id', $user->id)
                    ->whereHas('classroom.studentProfiles', fn ($q) => $q->where('user_id', $studentId))
                    ->exists(),
                403, 'Student not in your classrooms.'
            ),
            default => null,
        };

        $query = Attendance::where('student_user_id', $studentId)
            ->with(['student', 'classroom', 'scheduleSlot.subject', 'justification'])
            ->orderByDesc('date');

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        return response()->json($query->paginate(30));
    }
}
