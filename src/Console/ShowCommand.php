<?php
namespace Rainsens\Rbac\Console;

use Illuminate\Console\Command;

class ShowCommand extends Command
{
	protected $signature = 'rbac:show';
	
	protected $description = 'Show all permissions.';
	
	public function handle()
	{
		$this->info('show');
	}
}
