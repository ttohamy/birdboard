<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\Task ;

class ProjectTasksController extends Controller
{
    public function store(Project $project){
        $this->authorize('update' , $project);  
    	request()->validate(['body' => 'required']);
 		$project->addTask(request('body'));
 		return redirect($project->path());
    }
    public function update(Project $project , Task $task){
        $this->authorize('update' , $task->project);  
        request()->validate(['body'=>'required']);
    	$task->update(['body'=>request('body')]);
        $method = request('completed') ? $task->complete() : $task->incomplete() ;
        // $project->recordActivity('updated_task');
        // $task->$method();
        // if(request('completed')){
        //     $task->complete();
        // }else {
        //     $task->incomplete();
        // }
    	return 	redirect($project->path());
    }
}
