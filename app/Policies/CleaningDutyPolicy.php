<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\CleaningDuty;
use App\Models\User;

class CleaningDutyPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            Role::Admin,
            Role::Staff,
            Role::Student,
        ], true);
    }

    public function view(User $user, CleaningDuty $cleaningDuty): bool
    {
        if (in_array($user->role, [Role::Admin, Role::Staff], true)) {
            return true;
        }

        return $user->role === Role::Student
            && $cleaningDuty->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === Role::Staff;
    }

    public function update(User $user, CleaningDuty $cleaningDuty): bool
    {
        return $user->role === Role::Staff;
    }

    public function complete(User $user, CleaningDuty $cleaningDuty): bool
    {
        return $user->role === Role::Student
            && $cleaningDuty->user_id === $user->id
            && $cleaningDuty->completed_at === null;
    }

    public function delete(User $user, CleaningDuty $cleaningDuty): bool
    {
        return $user->role === Role::Staff;
    }
}
