<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Indicator;
use Illuminate\Auth\Access\HandlesAuthorization;

class IndicatorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Indicator');
    }

    public function view(AuthUser $authUser, Indicator $indicator): bool
    {
        return $authUser->can('View:Indicator');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Indicator');
    }

    public function update(AuthUser $authUser, Indicator $indicator): bool
    {
        return $authUser->can('Update:Indicator');
    }

    public function delete(AuthUser $authUser, Indicator $indicator): bool
    {
        return $authUser->can('Delete:Indicator');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Indicator');
    }

    public function restore(AuthUser $authUser, Indicator $indicator): bool
    {
        return $authUser->can('Restore:Indicator');
    }

    public function forceDelete(AuthUser $authUser, Indicator $indicator): bool
    {
        return $authUser->can('ForceDelete:Indicator');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Indicator');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Indicator');
    }

    public function replicate(AuthUser $authUser, Indicator $indicator): bool
    {
        return $authUser->can('Replicate:Indicator');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Indicator');
    }

}