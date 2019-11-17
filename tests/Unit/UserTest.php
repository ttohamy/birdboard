<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Facades\Tests\Setup\ProjectFactory;
use App\User ;  



class UserTest extends TestCase
{
	use RefreshDatabase;

	/**
	*@test
	*/
	public function a_user_has_projects(){
		$user = factory('App\User')->create();
		$this->assertInstanceOf(Collection::class,$user->projects);
	}
	/**
	*@test
	*/
	public function a_user_has_accessible_projects()
	{
		$user1 = $this->signIn();
		ProjectFactory::ownedBy($user1)->create();
		$this->assertCount(1,$user1->accessible_projects());
		$user2 = factory(User::class)->create();
		$user3 = factory(User::class)->create();
		$user2Project = ProjectFactory::ownedBy($user2)->create();
		$user2Project->invite($user3);
		$this->assertCount(1,$user1->accessible_projects());
		$user2Project->invite($user1);
		$this->assertCount(2,$user1->accessible_projects());
	}
}