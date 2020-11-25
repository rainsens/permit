<?php
namespace Rainsens\Permit\Tests\Unit;

use Rainsens\Rbac\Models\Permit;
use Rainsens\Rbac\Models\Role;
use Rainsens\Rbac\Tests\Dummy\Models\User;
use Rainsens\Rbac\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermitTest extends TestCase
{
	use RefreshDatabase;
	
	/** @test */
	public function can_create_a_new_permit()
	{
		$this->assertCount(0, Permit::all());
		$permit = Permit::create('edit articles');
		
		$this->assertCount(1, Permit::all());
		$this->assertEquals('edit articles', $permit->name);
	}
	
	/** @test */
	public function can_create_a_new_permit_with_guard_name()
	{
		$this->assertCount(0, Permit::all());
		$permit = Permit::create('edit articles', 'web');
		
		$this->assertCount(1, Permit::all());
		$this->assertEquals('web', $permit->guard);
	}
	
	/** @test */
	public function find_a_permit_by_name()
	{
		factory(Permit::class)->create([
			'id' => 1,
			'name' => 'Create Article',
			'path' => '/create-article'
		]);
		
		$permit = Permit::findByName('Create Article');
		$this->assertEquals('/create-article', $permit->path);
	}
	
	/** @test */
	public function find_a_permit_by_id()
	{
		factory(Permit::class)->create([
			'id' => 2,
			'name' => 'Edit Article',
			'path' => '/edit-article'
		]);
		
		$permit = Permit::findById(2);
		$this->assertEquals('/edit-article', $permit->path);
	}
	
	/** @test */
	public function can_give_permit_to_roles()
	{
		$permit = factory(Permit::class)->create([
			'id' => 1,
			'name' => 'Create Article',
			'path' => '/create-article'
		]);
		
		$r1 = factory(Role::class)->create([
			'id' => 2,
			'name' => 'editor'
		]);
		
		$r2 = factory(Role::class)->create([
			'id' => 3,
			'name' => 'author'
		]);
		
		$this->assertCount(0, $permit->roles);
		
		$permit->giveToRoles('editor', 'author');
		
		$this->assertCount(2, $permit->refresh()->roles);
	}
	
	/** @test */
	public function can_remove_permit_from_roles()
	{
		$permit = factory(Permit::class)->create([
			'id' => 1,
			'name' => 'Create Article',
			'path' => '/create-article'
		]);
		
		$r1 = factory(Role::class)->create([
			'id' => 2,
			'name' => 'editor'
		]);
		
		$r2 = factory(Role::class)->create([
			'id' => 3,
			'name' => 'author'
		]);
		
		$permit->giveToRoles($r1, $r2);
		$this->assertCount(2, $permit->refresh()->roles);
		
		$permit->removeFromRoles(2);
		$this->assertCount(1, $permit->refresh()->roles);
	}
	
	/** @test */
	public function can_check_if_a_permit_under_roles()
	{
		$role = factory(Role::class)->create([
			'id' => 1
		]);
		
		$permit = factory(Permit::class)->create([
			'id' => 1,
			'name' => 'Create Article',
			'path' => '/create-article'
		]);
		
		$this->assertFalse($permit->underRoles($role));
		
		$permit->giveToRoles($role);
		
		$this->assertTrue($permit->refresh()->underRoles($role));
	}
	
	/** @test */
	public function can_give_permit_to_users()
	{
		$permit = factory(Permit::class)->create([
			'id' => 1,
			'name' => 'Create Article',
			'path' => '/create-article'
		]);
		
		$u1 = factory(User::class)->create(['id' => 1]);
		$u2 = factory(User::class)->create(['id' => 2]);
		
		$this->assertCount(0, $permit->users);
		
		$permit->giveToUsers($u1, $u2);
		
		$this->assertCount(2, $permit->refresh()->users);
	}
	
	/** @test */
	public function can_remove_permit_from_users()
	{
		$permit = factory(Permit::class)->create([
			'id' => 1,
			'name' => 'Create Article',
			'path' => '/create-article'
		]);
		
		$u1 = factory(User::class)->create(['id' => 1]);
		$u2 = factory(User::class)->create(['id' => 2]);
		
		$this->assertCount(0, $permit->users);
		
		$permit->giveToUsers($u1, $u2);
		
		$this->assertCount(2, $permit->refresh()->users);
		
		$permit->removeFromUsers($u1);
		
		$this->assertCount(1, $permit->refresh()->users);
		$this->assertEquals(2, $permit->refresh()->users->first()->id);
	}
	
	/** @test */
	public function can_check_if_a_permit_under_users()
	{
		$user = factory(User::class)->create([
			'id' => 1
		]);
		
		$permit = factory(Permit::class)->create([
			'id' => 1,
			'name' => 'Create Article',
			'path' => '/create-article'
		]);
		
		$this->assertFalse($permit->underUsers($user));
		
		$permit->giveToUsers($user);
		
		$this->assertTrue($permit->refresh()->underUsers($user));
	}
	
	/** @test */
	public function user_has_permits()
	{
		$permit1 = createPermit(['id' => 1, 'name' => 'a']);
		$permit2 = createPermit(['id' => 2, 'name' => 'b']);
		
		$user = createUser();
		
		$this->assertCount(0, $user->permits);
		
		$permit1->giveToUsers($user);
		
		$this->assertCount(1, $user->refresh()->permits);
		
		$permit2->giveToUsers($user);
		
		$this->assertCount(2, $user->refresh()->permits);
	}
	
	/** @test */
	public function user_can_get_permits()
	{
		$permit = createPermit([
			'id' => 1,
			'name' => 'Create Article',
			'path' => '/create-article'
		]);
		
		$user = createUser();
		
		$this->assertCount(0, $user->permits);
		
		$user->givePermits($permit);
		
		$this->assertCount(1, $user->refresh()->permits);
	}
	
	/** @test */
	public function user_can_remove_permits()
	{
		$permit = createPermit([
			'id' => 1,
			'name' => 'Create Article',
			'path' => '/create-article'
		]);
		
		$user = createUser();
		
		$this->assertCount(0, $user->permits);
		
		$user->givePermits($permit);
		
		$this->assertCount(1, $user->refresh()->permits);
		
		$user->removePermits($permit);
		
		$this->assertCount(0, $user->refresh()->permits);
	}
	
	/** @test */
	public function can_check_if_a_user_had_permits()
	{
		$user = createUser();
		
		$permit = createPermit([
			'id' => 1,
			'name' => 'Create Article',
			'path' => '/create-article'
		]);
		
		$this->assertFalse($user->hasPermits($permit));
		
		$user->givePermits($permit);
		
		$this->assertTrue($user->refresh()->hasPermits($permit));
	}
}
