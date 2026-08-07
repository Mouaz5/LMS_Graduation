<?php

namespace App\Services\Parent;

use App\Models\Attendance;
use App\Models\User;

class ParentAttendanceService
{
    public function __construct(private ParentAccessService $access) {}

    public function records(
        User $parent,
        mixed $childId,
        ?string $dateFrom,
        ?string $dateTo,
    ): array {
        $children = $this->access->children($parent);
        $selectedChild = $this->access->selectChild($children, $childId);
        $records = collect();

        if ($selectedChild) {
            $query = Attendance::where('student_user_id', $selectedChild->id)
                ->with(['classroom', 'scheduleSlot.subject', 'justification'])
                ->orderByDesc('date');

            if ($dateFrom) {
                $query->whereDate('date', '>=', $dateFrom);
            }

            if ($dateTo) {
                $query->whereDate('date', '<=', $dateTo);
            }

            $records = $query->paginate(20)->withQueryString();
        }

        return compact('parent', 'children', 'selectedChild', 'records');
    }
}
