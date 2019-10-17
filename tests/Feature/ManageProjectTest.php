<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageProjectTest extends TestCase
{
    use withFaker ; 
    use RefreshDatabase ; 
    /**
    *@test
    */
    public function guests_cannot_manage_project(){
        // $this->withoutExceptionHandling(); 
        $project = factory('App\Project')->create();
        $this->post('/projects',$project->toArray())->assertRedirect('login');
        $this->get('/projects')->assertRedirect('login');
        $this->get('/projects/create')->assertRedirect('login');
        $this->get($project->path())->assertRedirect('login');
    }
    // /**
    // *@test
    // */
    // public function guests_cannot_view_projects(){
    //     $project = factory('App\Project')->create();
    // }
    // /**
    // *@test
    // */
    // public function guests_cannot_view_single_project(){
    //     $project = factory('App\Project')->create();
    // }



    
    /**
    *@test
    */ 
    public function a_user_can_create_a_project(){
        $this->withoutExceptionHandling(); 
        $this->signIn();
        $this->get('/projects/create')->assertStatus(200);
        $attributes = [
            'title'=> $this->faker->sentence ,
            'description' => $this->faker->paragraph  
        ];
        $this->post('/projects', $attributes)->assertRedirect('/projects');
        $this->assertDatabaseHas('projects',$attributes);
        $this->get('/projects')->assertSee($attributes['title']);
    }
    /**
    *@test
    */ 
    public function a_project_requires_a_title(){
        $this->signIn();
        $attributes = factory('App\Project')->raw(['title'=>'']);
        $this->post('/projects',$attributes)->assertSessionHasErrors('title');
    }
    /**
    *@test
    */ 
    public function a_project_requires_a_description(){
     $this->signIn();
     $attributes = factory('App\Project')->raw(['description'=>'']);
     $this->post('/projects',$attributes)->assertSessionHasErrors('description');
 }
    /**
    *@test
    */
    public function a_user_can_view_their_project(){
        $this->signIn();
        $this->withoutExceptionHandling(); 
        $project = factory('App\Project')->create(['owner_id' => auth()->id()]);
        $this->get($project->path())->assertSee($project->title)->assertSee($project->description);
    }

    /**
    *@test
    */ 
    public function an_authenticated_user_cannot_access_projects_of_other(){
        $this->be(factory('App\User')->create());
        // $this->withoutExceptionHandling(); 
        $project = factory('App\Project')->create();
        $this->get($project->path())->assertStatus(403);        
    }

    }
