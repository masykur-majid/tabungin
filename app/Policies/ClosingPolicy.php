<?php

namespace App\Policies;

use App\Enums\ClosingStatus;
use App\Models\Closing;
use App\Models\User;

class ClosingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Closing');
    }

    public function view(User $user, Closing $closing): bool
    {
        return $user->can('View:Closing')
            && $this->canAccessClosing($user, $closing);
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Closing');
    }

    public function update(User $user, Closing $closing): bool
    {
        return $user->can('Update:Closing')
            && $this->canAccessClosing($user, $closing)
            && $closing->status === ClosingStatus::Pending;
    }

    public function delete(User $user, Closing $closing): bool
    {
        return $user->can('Delete:Closing')
            && $this->canAccessClosing($user, $closing);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Closing');
    }

    public function restore(User $user, Closing $closing): bool
    {
        return $user->can('Restore:Closing')
            && $this->canAccessClosing($user, $closing);
    }

    public function forceDelete(User $user, Closing $closing): bool
    {
        return $user->can('ForceDelete:Closing')
            && $this->canAccessClosing($user, $closing);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Closing');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Closing');
    }

    public function replicate(User $user, Closing $closing): bool
    {
        return $user->can('Replicate:Closing')
            && $this->canAccessClosing($user, $closing);
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Closing');
    }

    private function canAccessClosing(User $user, Closing $closing): bool
    {
        if ($user->hasRole(['super_admin', 'supervisor'])) {
            return true;
        }

        return (int) $closing->user_id === (int) $user->id;
    }
}
