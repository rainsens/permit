<?php
namespace Rainsens\Rbac\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
	protected $signature = 'rbac:install';
	
	protected $description = 'Install Rbac package.';
	
	public function handle()
	{
		$this->check();
		$this->call('migrate');
	}
	
	protected function check()
	{
		// Check wether config file published.
		if (!config('rbac.guard')) {
			$errorNote = "Please run: 'php artisan rbac:config' first.\n";
			$this->error($errorNote);
			exit('Publish the config file and try it again.');
		}
	}
}
