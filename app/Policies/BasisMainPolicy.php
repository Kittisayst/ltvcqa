<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BasisMain;
use Illuminate\Auth\Access\HandlesAuthorization;

class BasisMainPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BasisMain');
    }

    public function view(AuthUser $authUser, BasisMain $basisMain): bool
    {
        return $authUser->can('View:BasisMain');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BasisMain');
    }

    public function update(AuthUser $authUser, BasisMain $basisMain): bool
    {
        return $authUser->can('Update:BasisMain');
    }

    public function delete(AuthUser $authUser, BasisMain $basisMain): bool
    {
        return $authUser->can('Delete:BasisMain');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BasisMain');
    }

    public function restore(AuthUser $authUser, BasisMain $basisMain): bool
    {
        return $authUser->can('Restore:BasisMain');
    }

    public function forceDelete(AuthUser $authUser, BasisMain $basisMain): bool
    {
        return $authUser->can('ForceDelete:BasisMain');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BasisMain');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BasisMain');
    }

    public function replicate(AuthUser $authUser, BasisMain $basisMain): bool
    {
        return $authUser->can('Replicate:BasisMain');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BasisMain');
    }

}