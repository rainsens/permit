<?php
namespace Rainsens\Authorize\Facades;

use Illuminate\Support\Facades\Facade;

class Authorize extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'authorize';
	}
}
