<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Project ;
use Facades\Tests\Setup\ProjectFactory; 


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
        $this->get($project->path().'/edit')->assertRedirect('login');
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
            'description' => $this->faker->sentence, 
            'notes' => 'this is global note'  
        ];
        $response = $this->post('/projects', $attributes);
        $project = Project::where($attributes)->first();
        $response->assertRedirect($project->path());
        $this->assertDatabaseHas('projects',$attributes);
        $this->get($project->path())->assertSee($attributes['title'])
        ->assertSee($attributes['description'])
        ->assertSee($attributes['notes']);
    }
    /**
    *@test
    */    
    public function a_user_can_update_a_project(){
        $project =  ProjectFactory::create();
        $this->actingAs($project->owner)->patch($project->path(), $attributes = [
           'title'=>'my title',
           'description'=>'new desc' ,
           'notes'=>'Changed']);
        $this->assertDatabaseHas('projects' , $attributes);

    }
    /**
    *@test
    */    
    public function a_user_can_update_general_notes(){
        $project =  ProjectFactory::create();
        $this->actingAs($project->owner)->patch($project->path(), $attributes = [
           'notes'=>'Changed']);
        $this->assertDatabaseHas('projects' , $attributes);

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
        // $this->withoutExceptionHandling(); 
        $project =  ProjectFactory::create();
        $this->actingAs($project->owner)->get($project->path())->assertSee($project->title)->assertSee($project->description);
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

    /**
    *@test
    */ 
    public function an_authenticated_user_cannot_update_projects_of_other(){
        $this->signIn();
        // $this->withoutExceptionHandling(); 
        $project = factory('App\Project')->create();
        $this->patch($project->path())->assertStatus(403);        
    }
    /**
    *@test
    */ 
    public function user_can_delete_project(){
        $this->withoutExceptionHandling();
        $project = ProjectFactory::create();
        $this->actingAs($project->owner)->delete($project->path())
        ->assertRedirect('/projects');
        $this->assertDatabaseMissing('projects' , $project->only('id'));    
    }
    /**
    *@test
    */ 
    public function guests_users_cannot_delete_project(){
        // $this->withoutExceptionHandling();
        $project = ProjectFactory::create();
        $this->delete($project->path())->assertRedirect('/login');
        // $this->assertDatabaseMissing('projects' , $project->only('id'));
        $this->signIn();
        $this->delete($project->path())->assertStatus(403) ;
    }
    /**
    *@test
    */ 
    public function a_user_can_see_all_project_they_have_been_invited_on_their_dashboard(){
        $user    = $this->signIn();
        $project = ProjectFactory::create();
        $project->invite($user);
        $this->get('/projects')->assertSee($project->title);
    }
}
