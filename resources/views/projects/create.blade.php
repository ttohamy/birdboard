
<!DOCTYPE html>
@extends('layouts.app')
@section('content')
<h1>Create New Project</h1>
<form method="POST" action="/projects" >
  @csrf
  <div class="form-group">
    <input type="field" name="title" placeholder="title" />
  </div>
  <div class="form-group">
    <input type="field"  name="description" placeholder="description" />
  </div>
  <button type="submit" >Submit</button>
  <a href="/projects">cancel</a>
</form>
@endsection 
