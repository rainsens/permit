<?php
namespace Rainsens\Permit\Facades;

use Illuminate\Support\Facades\Facade;

class Permit extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'permit';
	}
}
