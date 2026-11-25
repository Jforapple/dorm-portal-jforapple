<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\RoomCheck;
use App\Models\User;

class RoomCheckPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, RoomCheck $roomCheck): bool
    {
        if ($this->canManage($user)) {
            return true;
        }

        return $user->role === Role::Student
            && $user->room_id === $roomCheck->room_id;
    }

    public function create(User $user): bool
    {
        return $user->role === Role::Staff;
    }

    public function update(User $user, RoomCheck $roomCheck): bool
    {
        return $user->role === Role::Staff;
    }

    public function delete(User $user, RoomCheck $roomCheck): bool
    {
        return $this->canManage($user);
    }

    private function canManage(User $user): bool
    {
        return in_array($user->role, [
            Role::Admin,
            Role::Staff,
        ], true);
    }
}
