<!DOCTYPE html>
@extends('layouts.app')
@section('content')
<header class="flex items-center mb-3 py-3">
	<div class="flex justify-between items-end w-full">
		<p class="text-grey text-sm font-normal">
			<a href="/projects" class="text-grey text-sm font-normal no-underline" >My Porjects </a> / {{$project->title}}
		</p>
		<div class="flex items-center">
			@foreach($project->members as $member)
			<img src={{gravatar_url($member->email)}}
			alt="{{$member->name}}'s avatar" 
			class="rounded-full w-8 mr-2">
			@endforeach 
			<img src={{gravatar_url($project->owner->email)}}
			alt="{{$project->owner->email}}'s avatar" 
			class="rounded-full w-8 mr-2">

			<a class="button ml-4"  href="{{$project->path()}}/edit">Edit project</a>
		</div>
	</div>
</header>
<main>
	<div class="lg:flex -m-3 ">
		<div class="lg:w-3/4 px-3 mb-6">
			<div class="mb-8">
				<h2 class="text-lg text-grey font-normal mb-3">Tasks</h2>
				@foreach($project->tasks as $task)
				<div class="card mb-3">
					
					<form method="POST" action={{$task->path()}} >
						@method('PATCH')
						@csrf
						<div class="flex">
							<input class="w-full {{$task->completed ? 'text-grey' : ''}}"  name='body' value="{{$task->body}}" >
							<input type="checkbox" name="completed" onchange="this.form.submit()"
							{{$task->completed ? 'checked' :''}}>
						</div>
					</form>
				</div>
				@endforeach
				<div class="card mb-3">
					<form action="{{$project->path().'/tasks'}}" method="POST">
						@csrf
						<input type="field" name="body" class="w-full" placeholder="add new task ">
					</form>
				</div>	

			</div>
			<div>
				<h2 class="text-lg text-grey font-normal mb-3">General Notes</h2>
				<form method='POST' action={{$project->path()}}>
					@method('PATCH')
					@csrf
					<textarea class="card w-full mb-4" 
					style="min-height: 200px"  name="notes" 
					placeholder="add your notes">{{$project->notes}}</textarea>
					<button class="button" type="submit">Save</button>
				</form>
			</div>
		</div>
		<div class="lg:w-1/4 px-3">
			@include('projects.card')
			@include('projects.activity.card')
		</div>
	</div>
</main>
@if($errors->any())
<div class="field mt-4">
	@foreach($errors->all() as $error)
	<li class="text-sm text-red">{{$error}}</li>
	@endforeach
</div>
@endif

@endsection 
