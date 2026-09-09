<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DestinationPolicy
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

    public function destination_assign()
    {
        $permission = User::getPermissions();
        if(in_array('destination-assign', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }

    public function destination_list()
    {
        $permission = User::getPermissions();
        if(in_array('destination-list', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }

    public function destination_edit()
    {
        $permission = User::getPermissions();
        if(in_array('destination-edit', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }

    public function destination_status()
    {
        $permission = User::getPermissions();
        if(in_array('destination-status', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }
}
