<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Project;
use App\Task ;
use Facades\Tests\Setup\ProjectFactory; 

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
        $project = ProjectFactory::create();
        $attributes = factory('App\Task')->raw(['body'=>'']);
        $this->actingAs($project->owner)->post($project->path() . '/tasks', $attributes)->assertSessionHasErrors('body');
    }    

    /**
    *@test
    */
    public function only_the_owner_of_the_project_may_add_tasks(){
        $this->signIn();
        $project = factory('App\Project')->create();
        $this->post($project->path() . '/tasks', ['body'=>'Test Task'])
        ->assertStatus(403);
        $this->assertDatabaseMissing('tasks',['body'=>'Test Task']);
    }
    /**
    *@test
    */
    public function a_task_can_be_updated(){
        $this->withoutExceptionHandling();
        // $this->signIn();
        //$project = auth()->user()->projects()->create(factory(Project::class)->raw());
        // $task = $project->addTask('test task');
        //app(ProjectFactory::class)->withTasks(1)->create();
        $project = ProjectFactory::withTasks(1)->create();
        $task = $project->tasks->first();
        $this->actingAs($project->owner)->patch($task->path(), ['body'=>'changed' ,'completed' =>true]); 
        $this->assertDatabaseHas('tasks' ,['body'=>'changed' ,'completed' =>true] );

    } 
    /**
    *@test
    */    
    public function only_the_owner_of_the_project_may_update_tasks(){
        $this->signIn();
        // $project = factory('App\Project')->create(); 
        // $task = $project->addTask('test task');
        $project = ProjectFactory::withTasks(1)->create();
        $task = $project->tasks->first();
        $this->patch($task->path(), ['body'=>'changed'])->assertStatus(403);
        $this->assertDatabaseMissing('tasks',['body'=>'changed'] );

    }   


}
