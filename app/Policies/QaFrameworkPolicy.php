<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\QaFramework;
use Illuminate\Auth\Access\HandlesAuthorization;

class QaFrameworkPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:QaFramework');
    }

    public function view(AuthUser $authUser, QaFramework $qaFramework): bool
    {
        return $authUser->can('View:QaFramework');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:QaFramework');
    }

    public function update(AuthUser $authUser, QaFramework $qaFramework): bool
    {
        return $authUser->can('Update:QaFramework');
    }

    public function delete(AuthUser $authUser, QaFramework $qaFramework): bool
    {
        return $authUser->can('Delete:QaFramework');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:QaFramework');
    }

    public function restore(AuthUser $authUser, QaFramework $qaFramework): bool
    {
        return $authUser->can('Restore:QaFramework');
    }

    public function forceDelete(AuthUser $authUser, QaFramework $qaFramework): bool
    {
        return $authUser->can('ForceDelete:QaFramework');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:QaFramework');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:QaFramework');
    }

    public function replicate(AuthUser $authUser, QaFramework $qaFramework): bool
    {
        return $authUser->can('Replicate:QaFramework');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:QaFramework');
    }

}