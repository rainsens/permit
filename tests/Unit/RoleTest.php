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
	public function find_a_role_by_name()
	{
		factory(Role::class)->create([
			'id' => 1,
			'name' => 'Author',
		]);
		
		$role = Role::findByName('Author');
		$this->assertEquals('Author', $role->name);
	}
	
	/** @test */
	public function find_a_role_by_id()
	{
		factory(Role::class)->create([
			'id' => 2,
			'name' => 'Editor',
		]);
		
		$role = Role::findById(2);
		$this->assertEquals('Editor', $role->name);
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
	public function can_check_if_a_role_had_permits()
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
		
		$this->assertFalse($role->hasPermits($p1));
		
		$role->givePermits($p1);
		
		$this->assertTrue($role->refresh()->hasPermits($p1));
		
		$this->assertFalse($role->refresh()->hasPermits($p2));
	}
	
	/** @test */
	public function can_give_role_to_users()
	{
		$role = factory(Role::class)->create([
			'id' => 1,
			'name' => 'Author',
		]);
		
		$user1 = factory(User::class)->create([
			'id' => 1
		]);
		
		$user2 = factory(User::class)->create([
			'id' => 2
		]);
		
		$this->assertCount(0, $role->users);
		
		$role->giveToUsers($user1, $user2);
		
		$this->assertCount(2, $role->refresh()->users);
	}
	
	/** @test */
	public function can_remove_role_from_users()
	{
		$role = factory(Role::class)->create([
			'id' => 1,
			'name' => 'Author',
		]);
		
		$user1 = factory(User::class)->create([
			'id' => 1
		]);
		
		$user2 = factory(User::class)->create([
			'id' => 2
		]);
		
		$this->assertCount(0, $role->users);
		
		$role->giveToUsers($user1, $user2);
		$this->assertCount(2, $role->refresh()->users);
		
		$role->removeFromUsers($user1);
		$this->assertCount(1, $role->refresh()->users);
		$this->assertEquals(2, $role->refresh()->users->first()->id);
	}
	
	/** @test */
	public function can_check_if_a_role_under_users()
	{
		$user = createUser();
		
		$role = factory(Role::class)->create([
			'id' => 1
		]);
		
		$this->assertFalse($role->underUsers($user));
		
		$role->giveToUsers($user);
		
		$this->assertTrue($role->refresh()->underUsers($role));
	}
	
	/** @test */
	public function user_has_roles()
	{
		$role1 = createRole(['id' => 1, 'name' => 'a']);
		$role2 = createRole(['id' => 2, 'name' => 'b']);
		
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
		$role = createRole([
			'id' => 1,
			'name' => 'Author',
		]);
		
		$user = createUser();
		
		$this->assertCount(0, $user->roles);
		
		$user->giveRoles($role);
		
		$this->assertCount(1, $user->refresh()->roles);
	}
	
	/** @test */
	public function user_can_remove_roles()
	{
		$role = createRole([
			'id' => 1,
			'name' => 'Editor',
		]);
		
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
		
		$role = createRole([
			'id' => 1,
			'name' => 'Editor',
		]);
		
		$this->assertFalse($user->hasRoles($role));
		
		$user->giveRoles($role);
		
		$this->assertTrue($user->refresh()->hasRoles($role));
	}
}
