<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
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

    public function tag_create()
    {
        $permission = User::getPermissions();
        if(in_array('tag-create', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }

    public function tag_delete()
    {
        $permission = User::getPermissions();
        if(in_array('tag-delete', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }

    public function tag_edit()
    {
        $permission = User::getPermissions();
        if(in_array('tag-edit', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }
}
