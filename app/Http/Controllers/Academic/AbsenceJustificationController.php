<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\StoreAbsenceJustificationRequest;
use App\Http\Requests\Academic\UpdateAbsenceJustificationRequest;
use App\Models\AbsenceJustification;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AbsenceJustificationController extends Controller
{
    public function __construct(private AttendanceService $service) {}

    /**
     * POST /api/v1/absence-justifications
     * Role: parent only.
     */
    public function store(StoreAbsenceJustificationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $attendance = Attendance::findOrFail($validated['attendance_id']);
        $parent     = $request->user();

        abort_unless(
            $parent->children()->where('student_user_id', $attendance->student_user_id)->exists(),
            403,
            'You are not a parent of this student.'
        );

        abort_if(
            $attendance->justification()->exists(),
            422,
            'A justification already exists for this absence.'
        );

        $documentUrl = null;
        if ($request->hasFile('document')) {
            $path        = $request->file('document')->store('justifications', 'public');
            $documentUrl = Storage::url($path);
        }

        $justification = AbsenceJustification::create([
            'attendance_id' => $validated['attendance_id'],
            'reason'        => $validated['reason'],
            'submitted_by'  => $parent->id,
            'document_url'  => $documentUrl,
            'status'        => 'pending',
        ]);

        return response()->json($justification->load('attendance'), 201);
    }

    /**
     * PUT /api/v1/absence-justifications/{id}
     * Role: teacher only.
     *
     * Body: { action: 'approve' | 'reject' }
     */
    public function update(UpdateAbsenceJustificationRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $justification = AbsenceJustification::with('attendance.classroom')->findOrFail($id);

        $this->service->assertTeacherCanRecord(
            $request->user(),
            $justification->attendance->classroom_id,
            null
        );

        if ($validated['action'] === 'approve') {
            $this->service->approveJustification($justification);
        } else {
            $this->service->rejectJustification($justification);
        }

        return response()->json($justification->fresh(['attendance']));
    }
}
