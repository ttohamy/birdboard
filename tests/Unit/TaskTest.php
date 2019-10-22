<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Project ; 
use App\Task  ;

class TaskTest extends TestCase
{
	use RefreshDatabase;
    /**
    *@test
    */	
	public function it_has_a_path(){
		$task = factory('App\Task')->create();
		$this->assertEquals('/projects/'.$task->project->id.'/tasks/'.$task->id,$task->path());
	}
    /**
    *@test
    */
    public function its_belong_to_project(){
    	$task = factory(Task::class)->create();
    	$this->assertInstanceOf(Project::class , $task->project);
    }	
}
