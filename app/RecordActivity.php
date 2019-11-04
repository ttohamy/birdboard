<?php 
namespace App; 
use App\Activity ; 

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
			'description'=> $description ,
			'project_id' =>class_basename($this)==='Project'?$this->id : $this->project_id,
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
		return $this->morphMany(Activity::class , 'subject')->latest();
	}

	public static function recordableEvents(){
		if(isset(static::$recordableEvents)){
			return static::$recordableEvents;
		}else {
			return  ['created','updated','deleted']; 
		}

	}
	protected function activityDescription($description){
		return "{$description}_".strtolower(class_basename($this));
		
	}
}