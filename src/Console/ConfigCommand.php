<?php
namespace Rainsens\Rbac\Console;

use Illuminate\Console\Command;

class ConfigCommand extends Command
{
	protected $signature = 'rbac:config';
	
	protected $description = 'Publish Rbac config file.';
	
	public function handle()
	{
		$this->call('vendor:publish', [
			'--provider' => "Rainsens\Rbac\Providers\RbacServiceProvider",
			'--tag' => 'config'
		]);
	}
}
