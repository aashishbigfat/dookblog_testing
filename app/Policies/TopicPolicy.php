<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TopicPolicy
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

    public function topic_assign()
    {
        $permission = User::getPermissions();
        if(in_array('topic-assign', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }

    public function topic_view()
    {
        $permission = User::getPermissions();
        if(in_array('topic-view', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }

    public function topic_edit()
    {
        $permission = User::getPermissions();
        if(in_array('topic-edit', $permission)) {
            return true;
        }
        else{
            return false;
        }
    }
}
