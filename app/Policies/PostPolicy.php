<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function post_create()
    {
        $permission = User::getPermissions();
        if(in_array('post-create', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }

    public function post_view()
    {
        $permission = User::getPermissions();
        if(in_array('post-view', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }

    public function post_edit()
    {
        $permission = User::getPermissions();
        if(in_array('post-edit', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }

    public function post_delete()
    {
        $permission = User::getPermissions();
        if(in_array('post-delete', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }
}
