<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [Role::Admin, Role::Staff, Role::Student], true);
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if (in_array($user->role, [Role::Admin, Role::Staff], true)) {
            return true;
        }

        return $user->role === Role::Student
            && $attendance->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === Role::Staff;
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $user->role === Role::Staff;
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->role === Role::Staff;
    }
}
