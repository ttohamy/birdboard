<?php 
namespace App; 
use App\Activity ; 
use App\Project;
trait RecordActivity
{
	public $old = [] ; 
	public static function bootRecordActivity(){
		foreach (self::recordableEvents() as $event) {
			static::$event(function($model) use ($event) {
				$model->recordActivity($model->activityDescription($event)); 
			});
			if($event === 'updated'){
				static::updating(function($model)
				{
					$model->old = $model->getOriginal();
				});
			}
		}
	}
	public function recordActivity($description){
		$this->activity()->create([
			//if the model has a relationship with the project return the relation else return this
			'user_id' => ($this->project ??  $this)->owner->id ,
			'description'=> $description ,
			'project_id' =>(class_basename($this)==='Project')?$this->id : $this->project_id,
			'changes' => $this->activityChanges()
		]);
	}

	public function activityChanges()
	{
		if($this->wasChanged()){
			return [ 
				'before'=> array_except(array_diff($this->old , $this->getAttributes()) , 'updated_at') , 
				'after' => array_except($this->getChanges(),'updated_at')	
			] ; 
		}
	}
	public function activity(){
		if (get_class($this) === Project::class) {
			return $this->hasMany(Activity::class)->latest();
		}
		return $this->morphMany(Activity::class, 'subject')->latest();
	}

	public static function recordableEvents(){
		if(isset(static::$recordableEvents)){
			return static::$recordableEvents;
		}else {
			return  ['created','updated']; 
		}

	}
	protected function activityDescription($description){
		return "{$description}_".strtolower(class_basename($this));
		
	}
}