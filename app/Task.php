<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Project ;

class Task extends Model
{
	use RecordActivity ;
	public $old = [] ; 
	protected $guarded = [''];
	protected $touches = ['project'];
	protected $casts = ['completed'=> 'boolean'];

	public function project(){
		return $this->belongsTo(Project::class);
	}
	public function path(){
		return "/projects/{$this->project->id}/tasks/{$this->id}";
	}
	public function complete(){
		$this->update(['completed' => true ]);
		$this->recordActivity('completed_task');
	}
	public function incomplete(){
		$this->update(['completed' => false ]);
		$this->recordActivity('uncompleted_task');
	}
	public function activity(){
		return $this->morphMany(Activity::class , 'subject')->latest();
	}


	public function activityChanges()
	{
		return null ;
		if($this->wasChanged()){
			return [ 
				'before'=> array_except(array_diff($this->old , $this->getAttributes()) , 'updated_at') , 
				'after' => array_except($this->getChanges(),'updated_at')	
			] ; 
		}
	}


}
