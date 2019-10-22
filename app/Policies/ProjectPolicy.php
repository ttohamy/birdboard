<?php

namespace App\Policies;

use App\User;
use App\Project ; 
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __constructs()
    {
        //
    }

    public function update(User $user,Project $project ){
        return $user->is($project->owner);

    }

}
