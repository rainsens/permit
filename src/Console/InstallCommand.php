<?php
namespace Rainsens\Rbac\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

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
		if (! File::exists(config_path('rbac.php'))) {
			$errorNote = "Please run: 'php artisan rbac:config' first.\n";
			$this->error($errorNote);
			exit('Publish the config file and try it again.');
		}
	}
}
