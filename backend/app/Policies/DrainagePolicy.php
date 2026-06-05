<?php

namespace App\Policies;

use App\Models\Drainage;
use App\Models\User;

class DrainagePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Drainage $drainage): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_drainage');
    }

    public function update(User $user, Drainage $drainage): bool
    {
        return $user->hasPermissionTo('edit_drainage');
    }

    public function delete(User $user, Drainage $drainage): bool
    {
        return $user->hasPermissionTo('delete_drainage');
    }
}
