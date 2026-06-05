<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;

class NewsPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, News $news): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_news');
    }

    public function update(User $user, News $news): bool
    {
        return $user->id === $news->user_id || $user->hasPermissionTo('edit_news');
    }

    public function delete(User $user, News $news): bool
    {
        return $user->id === $news->user_id || $user->hasPermissionTo('delete_news');
    }
}
