<?php
namespace Rainsens\Rbac\Facades;

use Illuminate\Support\Facades\Facade;
use Rainsens\Rbac\Support\Authorize;
use Rainsens\Rbac\Support\Guard;

/**
 * Class Rbac
 * @package Rainsens\Rbac\Facades
 * @method static Guard guard()
 * @method static Authorize authorize()
 */
class Rbac extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'rbac';
	}
}
