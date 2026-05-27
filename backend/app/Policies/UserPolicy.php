<?php

namespace App\Policies;

use App\Models\TeacherSubjectClassroom;
use App\Models\User;

class UserPolicy
{
    /**
     * Whether $actor may read any student record (grades, attendance, behavioral notes,
     * knowledge map) belonging to $student.
     *
     * Teachers are restricted to their assigned classrooms; blanket teacher access is
     * intentionally blocked — use the admin role for cross-classroom workflows.
     */
    public function viewRecords(User $actor, User $student): bool
    {
        if ($actor->role === 'admin') {
            return true;
        }

        if ($actor->id === $student->id) {
            return true;
        }

        if ($actor->role === 'parent') {
            return $actor->children()->where('student_user_id', $student->id)->exists();
        }

        if ($actor->role === 'teacher') {
            return TeacherSubjectClassroom::where('teacher_user_id', $actor->id)
                ->whereHas('classroom.studentProfiles', fn ($q) => $q->where('user_id', $student->id))
                ->exists();
        }

        return false;
    }
}
