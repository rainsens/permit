<?php
namespace Rainsens\Permit\Tests\Unit;

use Rainsens\Rbac\Models\Permit;
use Rainsens\Rbac\Models\Role;
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
		
		$this->assertCount(0, $permit->roleItems);
		
		$permit->givePermitToRoles('editor', 'author');
		
		$this->assertCount(2, $permit->refresh()->roleItems);
	}
	
	/** @test */
	public function can_remove_permit_to_roles()
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
		
		$permit->givePermitToRoles('editor');
		$this->assertCount(1, $permit->refresh()->roleItems);
		
		$permit->removePermitToRoles(2);
		$this->assertCount(0, $permit->refresh()->roleItems);
	}
	
	/** @test */
	public function can_check_if_a_permit_had_a_role()
	{
		$role = factory(Role::class)->create([
			'id' => 1
		]);
		
		$permit = factory(Permit::class)->create([
			'id' => 1,
			'name' => 'Create Article',
			'path' => '/create-article'
		]);
		
		$this->assertFalse($permit->hasRoleItem($role));
		
		$permit->givePermitToRoles($role);
		
		$this->assertTrue($permit->refresh()->hasRoleItem($role));
	}
}
