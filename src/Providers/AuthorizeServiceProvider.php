<?php
namespace Rainsens\Authorize\Providers;

use Illuminate\Support\ServiceProvider;
use Rainsens\Authorize\Console\ShowCommand;
use Rainsens\Authorize\Authorize;

class AuthorizeServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('authorize', function ($app) {
			return new Authorize();
		});
	}
	
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->commands([
				ShowCommand::class,
			]);
			
			$this->publishes([_base_path('config/permit.php') => config_path('permit.php')]);
		}
		
		$this->permitMigrations();
	}
	
	protected function permitMigrations()
	{
		$this->loadMigrationsFrom(_base_path('database/migrations'));
	}
}
