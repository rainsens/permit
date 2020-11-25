<?php
namespace Rainsens\Rbac\Tests;

use Rainsens\Rbac\Facades\Rbac;
use Rainsens\Rbac\Providers\RbacServiceProvider;
use Rainsens\Rbac\Tests\Dummy\Models\User;

class TestCase extends \Orchestra\Testbench\TestCase
{
	public function setUp(): void
	{
		parent::setUp();
		$this->withFactories(_base_path('database/factories'));
	}
	
	protected function getPackageProviders($app)
	{
		return [
			RbacServiceProvider::class,
		];
	}
	
	protected function getPackageAliases($app)
	{
		return [
			'Rbac' => Rbac::class
		];
	}
	
	protected function getEnvironmentSetUp($app)
	{
		$rbacConfig = require _base_path('config/rbac.php');
		config(['rbac' => $rbacConfig]);
		config(['auth.providers.users.model' => User::class]);
	}
}
