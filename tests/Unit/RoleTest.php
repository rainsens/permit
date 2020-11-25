<?php
namespace Rainsens\Permit\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Rainsens\Authorize\Models\Permit;
use Rainsens\Authorize\Models\Role;
use Rainsens\Authorize\Tests\TestCase;

class RoleTest extends TestCase
{
	use RefreshDatabase;
	
	/** @test */
	public function can_create_a_new_role()
	{
		$this->assertCount(0, Role::all());
		
		$role = Role::create('Editor');
		
		$this->assertCount(1, Role::all());
		$this->assertEquals('Editor', $role->name);
	}
	
	/** @test */
	public function can_create_a_new_role_with_guard_name()
	{
		$this->assertCount(0, Role::all());
		
		$role = Role::create('Editor', 'web');
		
		$this->assertCount(1, Role::all());
		$this->assertEquals('web', $role->guard);
	}
	
	/** @test */
	public function can_give_permits_to_a_role()
	{
		$p1 = factory(Permit::class)->create([
			'id' => 1,
			'name' => 'Create Article',
			'path' => '/create-article'
		]);
		
		$p2 = factory(Permit::class)->create([
			'id' => 2,
			'name' => 'Edit Article',
			'path' => '/edit-article'
		]);
		
		$role = factory(Role::class)->create([
			'id' => 1
		]);
		
		$this->assertCount(0, $role->permitItems);
		
		$role->giveRolePermits($p1, $p2);
		
		$this->assertCount(2, $role->refresh()->permitItems);
	}
	
	/** @test */
	public function can_remove_permits_from_a_role()
	{
		$role = factory(Role::class)->create([
			'id' => 1
		]);
		
		$p1 = factory(Permit::class)->create([
			'id' => 1,
			'name' => 'Create Article',
			'path' => '/create-article'
		]);
		
		$p2 = factory(Permit::class)->create([
			'id' => 2,
			'name' => 'Edit Article',
			'path' => '/edit-article'
		]);
		
		$role->giveRolePermits($p1, $p2);
		
		$this->assertCount(2, $role->refresh()->permitItems);
		
		$role->removeRolePermits(1, 2);
		
		$this->assertCount(0, $role->refresh()->permitItems);
	}
	
	/** @test */
	public function can_check_if_a_role_had_a_permit()
	{
		$role = factory(Role::class)->create([
			'id' => 1
		]);
		
		$p1 = factory(Permit::class)->create([
			'id' => 1,
			'name' => 'Create Article',
			'path' => '/create-article'
		]);
		
		$p2 = factory(Permit::class)->create([
			'id' => 2,
			'name' => 'Edit Article',
			'path' => '/edit-article'
		]);
		
		$this->assertFalse($role->hasPermitItem($p1));
		
		$role->giveRolePermits($p1);
		
		$this->assertTrue($role->refresh()->hasPermitItem($p1));
		
		$this->assertFalse($role->refresh()->hasPermitItem($p2));
	}
}
