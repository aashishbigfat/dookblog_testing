<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
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

    public function category_create()
    {
        $permission = User::getPermissions();
        if(in_array('category-create', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }

    public function category_delete()
    {
        $permission = User::getPermissions();
        if(in_array('category-delete', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }

    public function category_edit()
    {
        $permission = User::getPermissions();
        if(in_array('category-edit', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }
}
