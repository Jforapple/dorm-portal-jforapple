<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Determine whether the user can view any events.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view an event.
     */
    public function view(User $user, Event $event): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create events.
     */
    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can update an event.
     */
    public function update(User $user, Event $event): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can delete an event.
     */
    public function delete(User $user, Event $event): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can manage events.
     */
    private function canManage(User $user): bool
    {
        return in_array($user->role, [
            Role::Admin,
            Role::Staff,
        ], true);
    }
}
