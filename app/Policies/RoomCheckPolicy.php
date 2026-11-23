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
        return true;
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, RoomCheck $roomCheck): bool
    {
        return $this->canManage($user);
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
