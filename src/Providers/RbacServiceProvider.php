<?php
namespace Rainsens\Rbac\Providers;

use Rainsens\Rbac\Contracts\PermitContract;
use Rainsens\Rbac\Contracts\RoleContract;
use Rainsens\Rbac\Rbac;
use Illuminate\Support\Facades\Gate;
use Rainsens\Rbac\Console\ShowCommand;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\Access\Authorizable;

class RbacServiceProvider extends ServiceProvider
{
	protected $rbacCommands = [
		ShowCommand::class,
	];
	
	public function register()
	{
		$this->app->bind('rbac', function ($app) {
			return new Rbac();
		});
	}
	
	public function boot()
	{
		if (! config('rbac.models')) return;
		$this->app->bind(PermitContract::class, config('rbac.models.permit'));
		$this->app->bind(RoleContract::class, config('rbac.models.role'));
		
		$this->commands($this->rbacCommands);
		$this->publishes([_base_path('config/rbac.php') => config_path('rbac.php')]);
		$this->permitMigrations();
		$this->permitGate();
	}
	
	protected function permitMigrations()
	{
		$this->loadMigrationsFrom(_base_path('database/migrations'));
	}
	
	protected function permitGate()
	{
		Gate::before(function (Authorizable $user, string $ability) {
			return $user->hasPathPermit() and $user->hasPermits($ability);
		});
	}
}
