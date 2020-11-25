<?php
namespace Rainsens\Rbac\Providers;

use Rainsens\Rbac\Rbac;
use Illuminate\Support\ServiceProvider;
use Rainsens\Rbac\Console\ShowCommand;
use Rainsens\Rbac\Support\Authorize;

class RbacServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('rbac', function ($app) {
			return new Rbac();
		});
	}
	
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->commands([
				ShowCommand::class,
			]);
			
			$this->publishes([_base_path('config/rbac.php') => config_path('rbac.php')]);
		}
		
		$this->permitMigrations();
	}
	
	protected function permitMigrations()
	{
		$this->loadMigrationsFrom(_base_path('database/migrations'));
	}
}
