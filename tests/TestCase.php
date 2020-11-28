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
		$this->withFactories(rbac_base_path('database/factories'));
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
		$rbacConfig = require rbac_base_path('config/rbac.php');
		config(['rbac' => $rbacConfig]);
		config(['auth.providers.users.model' => User::class]);
		
		include_once rbac_base_path('tests/Dummy/Database/Migrations/create_users_table.php');
		(new \CreateUsersTable())->up();
	}
}
