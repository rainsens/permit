<?php
namespace Rainsens\Permit\Tests;

use Rainsens\Permit\Providers\PermitServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
	public function setUp(): void
	{
		parent::setUp();
	}
	
	protected function getPackageProviders($app)
	{
		return [
			PermitServiceProvider::class,
		];
	}
	
	protected function getEnvironmentSetUp($app)
	{
	
	}
}
