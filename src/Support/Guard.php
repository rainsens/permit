<?php
namespace Rainsens\Rbac\Support;

use Rainsens\Rbac\Exceptions\GuardDoesNotExist;
use Rainsens\Rbac\Exceptions\GuardProviderDoesNotExist;

/**
 * Class Guard
 * @package Rainsens\Rbac\Support
 * @property $name
 * @property $providerName
 * @property $providerClass
 */
class Guard
{
	/**
	 * Guard name
	 */
	protected $name;
	/**
	 * Guard provider name
	 */
	protected $providerName;
	/**
	 * Guard provider model
	 */
	protected $providerClass;
	
	public function __construct()
	{
		$guardName = config('rbac.guard') ?? $this->defaultName();
		$guardProviderName = config("auth.guards.{$guardName}.provider");
		$guardProviderClass = config("auth.providers.{$guardProviderName}.model");
		
		if (! isset($guardProviderName) || ! isset($guardProviderClass)) {
			throw new GuardDoesNotExist('Guard name provided is not existing.');
		}
		
		try {
			app($guardProviderClass);
		} catch (\Exception $e) {
			throw new GuardProviderDoesNotExist('Guard provider is not existing.');
		}
		
		$this->name = $guardName;
		$this->providerName = $guardProviderName;
		$this->providerClass = $guardProviderClass;
	}
	
	/**
	 * Default guard name.
	 * @return string
	 */
	public function defaultName(): string
	{
		return config('auth.defaults.guard');
	}
	
	public function __get($name)
	{
		return isset($this->$name) ? $this->$name : null;
	}
}
