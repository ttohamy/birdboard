<?php 
namespace app ; 

trait RecordActivity
{

	public function recordActivity($description){
		$this->activity()->create([
			'description'=> $description ,
			'project_id' =>class_basename($this)==='Project' ?$this->id : $this->project_id,
			'changes' => $this->activityChanges()
		]);
	}
}