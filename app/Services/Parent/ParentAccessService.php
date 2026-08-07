<?php

namespace App\Services\Parent;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;

class ParentAccessService
{
    public function children(User $parent): Collection
    {
        return $parent->children()
            ->with('studentProfile.classroom.grade')
            ->get();
    }

    public function selectChild(Collection $children, mixed $childId): ?User
    {
        if ($childId === null) {
            return $children->first();
        }

        return $children->first(
            fn (User $child): bool => (string) $child->id === (string) $childId
        );
    }

    public function findChild(User $parent, mixed $childId): ?User
    {
        $query = $parent->children()->with('studentProfile.classroom.grade');

        if ($childId !== null) {
            $query->whereKey($childId);
        }

        return $query->first();
    }

    public function child(User $parent, User $child, ?string $message = null): User
    {
        // Verify this child belongs to the parent
        $authorizedChild = $parent->children()
            ->with('studentProfile.classroom.grade')
            ->whereKey($child->getKey())
            ->first();

        if (! $authorizedChild) {
            throw new AuthorizationException(
                $message ?? __('This student is not linked to your account.')
            );
        }

        return $authorizedChild;
    }

    public function assertStudentBelongsToParent(
        User $parent,
        int $studentId,
        ?string $message = null
    ): void {
        $belongsToParent = $parent->children()
            ->where('student_user_id', $studentId)
            ->exists();

        if (! $belongsToParent) {
            throw new AuthorizationException(
                $message ?? __('This student is not linked to your account.')
            );
        }
    }
}
