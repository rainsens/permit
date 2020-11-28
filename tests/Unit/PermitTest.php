<?php
namespace Rainsens\Permit\Tests\Unit;

use Rainsens\Rbac\Models\Permit;
use Rainsens\Rbac\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermitTest extends TestCase
{
	use RefreshDatabase;
	
	/** @test */
	public function can_create_a_new_permit()
	{
		$this->assertCount(0, Permit::all());
		$permit = Permit::create([
			'name' => 'edit articles',
			'slug' => 'create-articles'
		]);
		
		$this->assertCount(1, Permit::all());
		$this->assertEquals('edit articles', $permit->name);
		$this->assertEquals('create-articles', $permit->slug);
	}
	
	/** @test */
	public function can_create_a_new_permit_with_path()
	{
		$this->withoutExceptionHandling();
		
		$this->assertCount(0, Permit::all());
		
		$permit1 = Permit::create([
			'name' => 'create products',
			'slug' => 'create-products',
			'path' => '/products',
			'method' => 'get'
		]);
		
		$this->assertCount(1, Permit::all());
		$this->assertEquals('products', $permit1->path[0]);
		$this->assertEquals('GET', $permit1->method[0]);
		
		$permit2 = Permit::create([
			'name' => 'create orders',
			'slug' => 'create-orders',
			'path' => '/orders',
		]);
		
		$this->assertCount(2, Permit::all());
		$this->assertEquals('orders', $permit2->path[0]);
		$this->assertEquals(null, $permit2->method);
	}
	
	/** @test */
	public function can_find_a_permit_by_name()
	{
		createPermit([
			'name' => 'Create Article',
			'slug' => 'create-article',
			'path' => '/create-article',
		]);
		
		$permit = Permit::findByName('Create Article');
		$this->assertEquals('create-article', $permit->path[0]);
	}
	
	/** @test */
	public function can_find_a_permit_by_id()
	{
		createPermit([
			'id' => 1,
			'name' => 'Create Article',
			'slug' => 'create-article',
			'path' => '/create-article',
		]);
		
		$permit = Permit::findById(1);
		$this->assertEquals('create-article', $permit->path[0]);
	}
	
	/** @test */
	public function can_give_permit_to_roles()
	{
		$permit = createPermit();
		
		$role1 = createRole([
			'id' => 1,
			'name' => 'editor',
			'slug' => 'editor',
		]);
		
		$role2 = createRole([
			'id' => 2,
			'name' => 'author',
			'slug' => 'author',
		]);
		
		$this->assertCount(0, $permit->roles);
		$permit->giveToRoles('editor', 'author');
		
		$this->assertCount(2, $permit->refresh()->roles);
	}
	
	/** @test */
	public function can_remove_permit_from_roles()
	{
		$permit = createPermit([
			'id' => 1,
			'name' => 'Create Article',
			'slug' => 'create-article',
			'path' => '/create-article',
		]);
		
		$role1 = createRole([
			'id' => 1,
			'name' => 'author',
			'slug' => 'author',
		]);
		
		$role2 = createRole([
			'id' => 2,
			'name' => 'editor',
			'slug' => 'editor',
		]);
		
		$permit->giveToRoles($role1, $role2);
		$this->assertCount(2, $permit->refresh()->roles);
		
		$permit->removeFromRoles(2);
		$this->assertCount(1, $permit->refresh()->roles);
	}
	
	/** @test */
	public function can_check_if_a_permit_in_roles()
	{
		$role = createRole();
		$permit = createPermit();
		
		$this->assertFalse($permit->inRoles($role));
		
		$permit->giveToRoles($role);
		
		$this->assertTrue($permit->refresh()->inRoles($role));
	}
	
	/** @test */
	public function can_give_permit_to_users()
	{
		$permit = createPermit();
		
		$u1 = createUser(['id' => 1]);
		$u2 = createUser(['id' => 2]);
		
		$this->assertCount(0, $permit->users);
		
		$permit->giveToUsers($u1, $u2);
		
		$this->assertCount(2, $permit->refresh()->users);
	}
	
	/** @test */
	public function can_remove_permit_from_users()
	{
		$permit = createPermit();
		
		$u1 = createUser(['id' => 1]);
		$u2 = createUser(['id' => 2]);
		
		$this->assertCount(0, $permit->users);
		
		$permit->giveToUsers($u1, $u2);
		
		$this->assertCount(2, $permit->refresh()->users);
		
		$permit->removeFromUsers($u1);
		
		$this->assertCount(1, $permit->refresh()->users);
		$this->assertEquals(2, $permit->refresh()->users->first()->id);
	}
	
	/** @test */
	public function can_check_if_a_permit_in_users()
	{
		$user = createUser();
		
		$permit = createPermit();
		
		$this->assertFalse($permit->inUsers($user));
		
		$permit->giveToUsers($user);
		
		$this->assertTrue($permit->refresh()->inUsers($user));
	}
	
	/** @test */
	public function user_has_permits()
	{
		$permit1 = createPermit(['name' => 'a', 'slug' => 'a-a']);
		$permit2 = createPermit(['name' => 'b', 'slug' => 'b-b']);
		
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
			'slug' => 'create-article',
			'path' => '/create-article',
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
			'name' => 'Create Article',
			'slug' => 'create-article',
			'path' => '/create-article',
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
			'name' => 'Create Article',
			'slug' => 'create-article',
			'path' => '/create-article',
		]);
		
		$this->assertFalse($user->hasPermits($permit));
		
		$user->givePermits($permit);
		
		$this->assertTrue($user->refresh()->hasPermits($permit));
	}
}
