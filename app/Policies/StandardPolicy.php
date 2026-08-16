<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Standard;
use Illuminate\Auth\Access\HandlesAuthorization;

class StandardPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Standard');
    }

    public function view(AuthUser $authUser, Standard $standard): bool
    {
        return $authUser->can('View:Standard');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Standard');
    }

    public function update(AuthUser $authUser, Standard $standard): bool
    {
        return $authUser->can('Update:Standard');
    }

    public function delete(AuthUser $authUser, Standard $standard): bool
    {
        return $authUser->can('Delete:Standard');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Standard');
    }

    public function restore(AuthUser $authUser, Standard $standard): bool
    {
        return $authUser->can('Restore:Standard');
    }

    public function forceDelete(AuthUser $authUser, Standard $standard): bool
    {
        return $authUser->can('ForceDelete:Standard');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Standard');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Standard');
    }

    public function replicate(AuthUser $authUser, Standard $standard): bool
    {
        return $authUser->can('Replicate:Standard');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Standard');
    }

}