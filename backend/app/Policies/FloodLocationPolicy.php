<?php

namespace App\Policies;

use App\Models\FloodLocation;
use App\Models\User;

class FloodLocationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, FloodLocation $flood): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_flood');
    }

    public function update(User $user, FloodLocation $flood): bool
    {
        return $user->hasPermissionTo('edit_flood');
    }

    public function delete(User $user, FloodLocation $flood): bool
    {
        return $user->hasPermissionTo('delete_flood');
    }
}
