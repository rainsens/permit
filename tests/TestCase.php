<?php
namespace Rainsens\Authorize\Tests;

use Rainsens\Authorize\Facades\Authorize;
use Rainsens\Authorize\Providers\AuthorizeServiceProvider;

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
			AuthorizeServiceProvider::class,
		];
	}
	
	protected function getPackageAliases($app)
	{
		return [
			'Authorize' => Authorize::class
		];
	}
	
	protected function getEnvironmentSetUp($app)
	{
		$permitsConfig = require _base_path('config/authorize.php');
		config(['authorize' => $permitsConfig]);
	}
}
