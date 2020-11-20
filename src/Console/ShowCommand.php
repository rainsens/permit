<?php
namespace Rainsens\Permit\Console;

use Illuminate\Console\Command;

class ShowCommand extends Command
{
	protected $signature = 'permit:show';
	
	protected $description = 'Show all permissions.';
	
	public function handle()
	{
		$this->info('show');
	}
}
