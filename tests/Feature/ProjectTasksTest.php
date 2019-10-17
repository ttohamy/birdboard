<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Project;
use App\Task ;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;

    /**
    *@test
    */
    public function a_project_can_have_a_tasks()
    {
        $this->withoutExceptionHandling();
        $this->signIn();
        $project = factory(Project::class)->create(['owner_id'=>auth()->id()]);
        // $task = $project->addTask(['body'=>'Lorem Ipsum']);
        $this->post($project->path().'/tasks',['body'=>'project tasks']);
        $this->get($project->path())->assertSee('project tasks');

        
    }

    /**
    *@test
    */
    public function a_task_requires_a_body(){
        $this->signIn();
        $project = factory('App\Project')->create(['owner_id' => auth()->id()]);
        $attributes = factory('App\Task')->raw(['body'=>'']);
        $this->post($project->path() . '/tasks', $attributes)->assertSessionHasErrors('body');



    }    

}
