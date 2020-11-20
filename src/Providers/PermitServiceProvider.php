<?php
namespace Rainsens\Permit\Providers;

use Illuminate\Support\ServiceProvider;
use Rainsens\Permit\Console\ShowCommand;
use Rainsens\Permit\Permit;

class PermitServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('permit', function ($app) {
			return new Permit();
		});
	}
	
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->commands([
				ShowCommand::class,
			]);
			
			$this->publishes([__DIR__ . '/../../config/permit.php' => config_path('permit.php')]);
		}
	}
}
