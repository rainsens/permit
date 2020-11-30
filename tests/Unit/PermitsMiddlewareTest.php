<?php
namespace Rainsens\Rbac\Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Rainsens\Rbac\Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Rainsens\Rbac\Middleware\Permits;
use Rainsens\Rbac\Tests\Dummy\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Rainsens\Rbac\Exceptions\UnauthorizedException;

class PermitsMiddlewareTest extends TestCase
{
	use RefreshDatabase;
	
	/** @test */
	public function guest_cannot_pass()
	{
		$this->expectException(UnauthorizedException::class);
		
		$request = new Request();
		
		(new Permits())->handle($request, function ($request) {});
		
	}
	
	/** @test */
	public function authenticated_user_can_pass()
	{
		$user = factory(User::class)->create([
			'name' => 'Rainsen',
		]);
		$this->actingAs($user);
		$request = new Request();
		(new Permits())->handle($request, function ($request) {
			$this->assertEquals('Rainsen', Auth::user()->name);
		});
	}
	
	/** @test */
	public function user_cannot_pass_with_wrong_path_permit()
	{
		$this->withoutExceptionHandling();
		
		$this->expectException(UnauthorizedException::class);
		
		$user = factory(User::class)->create([
			'name' => 'Rainsen',
		]);
		$this->actingAs($user);
		
		$permit = createPermit([
			'name' => 'Create Article',
			'slug' => 'create-article',
			'path' => 'create',
			'method' => 'GET'
		]);
		
		Route::middleware('permits')->get('create', function () {});
		
		$this->get('create');
	}
	
	/** @test */
	public function user_can_pass_with_right_path_permit()
	{
		$user = factory(User::class)->create([
			'name' => 'Rainsen',
		]);
		
		$this->actingAs($user);
		
		$permit = createPermit([
			'name' => 'Create Article',
			'slug' => 'create-article',
			'path' => 'create',
			'method' => 'GET'
		]);
		
		$user->givePermits($permit);
		
		$request = new Request();
		
		(new Permits())->handle($request, function () {
			$this->assertEquals('Rainsen', Auth::user()->name);
		});
	}
}
