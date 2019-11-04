<?php
namespace Tests\Feature;
namespace Tests\Setup ; 

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Facades\Tests\Setup\ProjectFactory;
use App\Task ;
use App\activity;

class TriggerActivityTest extends TestCase
{
    use RefreshDatabase;
    /**
    *@test
    */
    public function creating_project(){
        $project = ProjectFactory::create();
        $this->assertCount(1,$project->activity);
        tap($project->activity->last(), function($activity){
            $this->assertEquals($activity->description,'created_project');
            $this->assertNull($activity->changes); 
        });

    }   
    /**
    *@test
    */
    public function updating_project(){
        $project = ProjectFactory::create();
        $originalTitle = $project->title ; 
        $project->update(['title' => 'changed']);
        $this->assertCount(2,$project->activity);
        tap($project->activity->last(), function($activity) use($originalTitle){
            $this->assertEquals($activity->description,'updated_project'); 
            $expected = [
                'before' => ['title' => $originalTitle] , 
                'after' => ['title' =>'changed' ]
            ];
            $this->assertEquals($expected , $activity->changes); 
        });

    }  
    /**
    *@test
    */ 
    public function completing_a_task(){
        $project = ProjectFactory::withTasks(1)->create();
        $this->actingAs($project->owner)->patch($project->tasks[0]->path(), [
            'body' => 'changed' ,
            'completed' =>true 
        ]);
        $this->assertCount(3,$project->activity);
        $this->assertEquals('completed_task' , $project->activity->last()->description);
    }
    /**
    *@test
    */
    public function creating_a_task(){
        $project = ProjectFactory::withTasks(1)->create();
        $this->assertCount(2,$project->activity);
        // $this->assertEquals('created_task' , $project->activity->last()->description);
        tap($project->activity->last() , function ($activity){
            $this->assertEquals('created_task' , $activity->description);
            $this->assertInstanceOf(Task::class , $activity->subject); 
        } );
    }
    /**
    *@test
    */ 
    public function incompleting_a_task(){
        $project = ProjectFactory::withTasks(1)->create();
        $this->actingAs($project->owner)->patch($project->tasks[0]->path(), [
            'body' => 'changed' ,
            'completed' =>true 
        ]);
        $this->assertCount(3,$project->activity);
        $this->patch($project->tasks[0]->path(), [
            'body' => 'changed' ,
            'completed' =>false 
        ]);
        $this->assertCount(4,$project->fresh()->activity);
        $this->assertEquals('uncompleted_task' , $project->fresh()->activity->last()->description);
    }
    /**
    *@test
    */ 
    public function delete_a_task(){
        $project = ProjectFactory::withTasks(1)->create();
        $project->tasks[0]->delete();
        $this->assertCount(3,$project->activity);
        $this->assertEquals('deleted_task' , $project->activity->last()->description);
    }
}
