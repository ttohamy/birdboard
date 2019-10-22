<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;

class ProjectController extends Controller
{
	public function index(){
		//$projects = Project::all();
		$projects = auth()->user()->projects;
		return view('projects.index',compact('projects'));
	}
	public function store(){
		
		$project = auth()->user()->projects()->create($this->validateRequest());
		//Project::create($attributes);
		return redirect($project->path());
	}
	public function show(Project $project){
		$this->authorize('update' , $project);	
		return view('projects.show',compact('project'));
	}
	public function create(){
		return view('projects.create');
	}
	public function update(Project $project){
		// if(auth()->user()->isNot($project->owner)){
		// 	abort(403);
		// }
		$this->authorize('update' , $project);	

		$project->update($this->validateRequest());
		return redirect($project->path());
	}
	public function edit(Project $project)
	{
		return view('projects.edit',compact('project'));
		
	}
	public function validateRequest(){
		$attributes = request()->validate([
			'title' => 'sometimes|required',
			'description' => 'sometimes|required' ,
			'notes' => 'nullable'
		]);
		return $attributes;
	}
}
