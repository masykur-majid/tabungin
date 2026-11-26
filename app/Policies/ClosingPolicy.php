<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Closing;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClosingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Closing');
    }

    public function view(AuthUser $authUser, Closing $closing): bool
    {
        return $authUser->can('View:Closing');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Closing');
    }

    public function update(AuthUser $authUser, Closing $closing): bool
    {
        return $authUser->can('Update:Closing');
    }

    public function delete(AuthUser $authUser, Closing $closing): bool
    {
        return $authUser->can('Delete:Closing');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Closing');
    }

    public function restore(AuthUser $authUser, Closing $closing): bool
    {
        return $authUser->can('Restore:Closing');
    }

    public function forceDelete(AuthUser $authUser, Closing $closing): bool
    {
        return $authUser->can('ForceDelete:Closing');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Closing');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Closing');
    }

    public function replicate(AuthUser $authUser, Closing $closing): bool
    {
        return $authUser->can('Replicate:Closing');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Closing');
    }

}