<?php
namespace Rainsens\Permit\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Rainsens\Rbac\Models\Permit;
use Rainsens\Rbac\Models\Role;
use Rainsens\Rbac\Tests\TestCase;

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
		
		$this->assertCount(0, $role->permits);
		
		$role->givePermits($p1, $p2);
		
		$this->assertCount(2, $role->refresh()->permits);
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
		
		$role->givePermits($p1, $p2);
		
		$this->assertCount(2, $role->refresh()->permits);
		
		$role->removePermits(1, 2);
		
		$this->assertCount(0, $role->refresh()->permits);
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
		
		$this->assertFalse($role->hasPermit($p1));
		
		$role->givePermits($p1);
		
		$this->assertTrue($role->refresh()->hasPermit($p1));
		
		$this->assertFalse($role->refresh()->hasPermit($p2));
	}
}
