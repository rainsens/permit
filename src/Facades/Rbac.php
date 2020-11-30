<?php
namespace Rainsens\Rbac\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Rainsens\Rbac\Support\Authorize;
use Rainsens\Rbac\Support\Guard;
use Rainsens\Rbac\Support\Supplier;

/**
 * Class Rbac
 * @package Rainsens\Rbac\Facades
 * @method static Authorize authorize()
 * @method static Guard guard()
 * @method static Supplier supplier()
 * @method static Model permit()
 */
class Rbac extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'rbac';
	}
}
