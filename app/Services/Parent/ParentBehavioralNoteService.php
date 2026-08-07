<?php

namespace App\Services\Parent;

use App\Models\BehavioralNote;
use App\Models\User;

class ParentBehavioralNoteService
{
    public function __construct(private ParentAccessService $access) {}

    public function forParent(User $parent, mixed $childId): array
    {
        $children = $this->access->children($parent);
        $selectedChild = $this->access->selectChild($children, $childId);
        $notes = collect();

        if ($selectedChild) {
            $notes = BehavioralNote::where('student_user_id', $selectedChild->id)
                ->with('teacher')
                ->orderByDesc('date')
                ->paginate(20);
        }

        return compact('parent', 'children', 'selectedChild', 'notes');
    }
}
