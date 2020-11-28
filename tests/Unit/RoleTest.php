<?php
namespace Rainsens\Permit\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Rainsens\Rbac\Models\Permit;
use Rainsens\Rbac\Models\Role;
use Rainsens\Rbac\Tests\TestCase;
use Rainsens\Rbac\Tests\Dummy\Models\User;

class RoleTest extends TestCase
{
	use RefreshDatabase;
	
	/** @test */
	public function can_create_a_new_role()
	{
		$this->assertCount(0, Role::all());
		
		$role = Role::create(['name' => 'Editor', 'slug' => 'editor']);
		
		$this->assertCount(1, Role::all());
		$this->assertEquals('Editor', $role->name);
	}
	
	/** @test */
	public function find_a_role_by_name()
	{
		createRole(['name' => 'Author', 'slug' => 'author']);
		
		$role = Role::findByName('Author');
		$this->assertEquals('Author', $role->name);
	}
	
	/** @test */
	public function find_a_role_by_id()
	{
		createRole(['id' => 2, 'name' => 'Editor', 'slug' => 'editor']);
		
		$role = Role::findById(2);
		$this->assertEquals('Editor', $role->name);
	}
	
	/** @test */
	public function can_give_permits_to_a_role()
	{
		$permit1 = createPermit([
			'name' => 'Create Article',
			'slug' => 'create-article',
			'path' => '/create-article'
		]);
		
		$permit2 = createPermit([
			'name' => 'Edit Article',
			'slug' => 'edit-article',
			'path' => '/edit-article'
		]);
		
		$role = createRole();
		
		$this->assertCount(0, $role->permits);
		
		$role->givePermits($permit1, $permit2);
		
		$this->assertCount(2, $role->refresh()->permits);
	}
	
	/** @test */
	public function can_remove_permits_from_a_role()
	{
		$role = createRole();
		
		$permit1 = createPermit([
			'name' => 'Create Article',
			'slug' => 'create-article',
			'path' => '/create-article',
		]);
		
		$permit2 = createPermit([
			'name' => 'Edit Article',
			'slug' => 'edit-article',
			'path' => '/edit-article',
		]);
		
		$role->givePermits($permit1, $permit2);
		
		$this->assertCount(2, $role->refresh()->permits);
		
		$role->removePermits(1, 2);
		
		$this->assertCount(0, $role->refresh()->permits);
	}
	
	/** @test */
	public function can_check_if_a_role_had_permits()
	{
		$role = createRole();
		
		$permit1 = createPermit([
			'name' => 'Create Article',
			'slug' => 'create-article',
			'path' => '/create-article',
		]);
		
		$permit2 = createPermit([
			'name' => 'Edit Article',
			'slug' => 'edit-article',
			'path' => '/edit-article',
		]);
		
		$this->assertFalse($role->hasPermits($permit1));
		
		$role->givePermits($permit1);
		
		$this->assertTrue($role->refresh()->hasPermits($permit1));
		
		$this->assertFalse($role->refresh()->hasPermits($permit2));
	}
	
	/** @test */
	public function can_give_role_to_users()
	{
		$role = createRole();
		
		$user1 = createUser();
		$user2 = createUser();
		
		$this->assertCount(0, $role->users);
		
		$role->giveToUsers($user1, $user2);
		
		$this->assertCount(2, $role->refresh()->users);
	}
	
	/** @test */
	public function can_remove_role_from_users()
	{
		$role = createRole();
		
		$user1 = createUser(['id' => 1]);
		
		$user2 = createUser(['id' => 2]);
		
		$this->assertCount(0, $role->users);
		
		$role->giveToUsers($user1, $user2);
		$this->assertCount(2, $role->refresh()->users);
		
		$role->removeFromUsers($user1);
		$this->assertCount(1, $role->refresh()->users);
		$this->assertEquals(2, $role->refresh()->users->first()->id);
	}
	
	/** @test */
	public function can_check_if_a_role_in_users()
	{
		$user = createUser();
		
		$role = createRole();
		
		$this->assertFalse($role->inUsers($user));
		
		$role->giveToUsers($user);
		
		$this->assertTrue($role->refresh()->inUsers($role));
	}
	
	/** @test */
	public function user_has_roles()
	{
		$role1 = createRole();
		$role2 = createRole();
		
		$user = createUser();
		
		$this->assertCount(0, $user->roles);
		
		$role1->giveToUsers($user);
		
		$this->assertCount(1, $user->refresh()->roles);
		
		$role2->giveToUsers($user);
		
		$this->assertCount(2, $user->refresh()->roles);
	}
	
	/** @test */
	public function user_can_get_roles()
	{
		$role = createRole();
		$user = createUser();
		
		$this->assertCount(0, $user->roles);
		
		$user->giveRoles($role);
		
		$this->assertCount(1, $user->refresh()->roles);
	}
	
	/** @test */
	public function user_can_remove_roles()
	{
		$role = createRole();
		
		$user = createUser();
		
		$this->assertCount(0, $user->roles);
		
		$user->giveRoles($role);
		
		$this->assertCount(1, $user->refresh()->roles);
		
		$user->removeRoles($role);
		
		$this->assertCount(0, $user->refresh()->roles);
	}
	
	/** @test */
	public function can_check_if_a_user_had_roles()
	{
		$user = createUser();
		$role = createRole();
		
		$this->assertFalse($user->hasRoles($role));
		
		$user->giveRoles($role);
		
		$this->assertTrue($user->refresh()->hasRoles($role));
	}
}
