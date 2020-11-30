<?php
namespace Rainsens\Rbac\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Rainsens\Rbac\Contracts\PermitContract;
use Rainsens\Rbac\Contracts\RoleContract;
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
		$guardName = Auth::getDefaultDriver();
		$guardProviderName = config("auth.guards.{$guardName}.provider");
		$guardProviderClass = config("auth.providers.{$guardProviderName}.model");
		
		if (! isset($guardProviderName) || ! isset($guardProviderClass)) {
			throw new GuardDoesNotExist('Guard name provided is not existing.');
		}
		
		$this->name = $guardName;
		$this->providerName = $guardProviderName;
		$this->providerClass = $guardProviderClass;
	}
	
	/**
	 * 1. Part 1 check whether given permits or roles have right specified guard name,
	 * 2. Part 2 check whether given examin gist within Rbac guard,
	 * then return the valid records.
	 *
	 * @param Collection $records
	 * @param null $examineGist
	 * @return Collection
	 */
	public function examine(Collection $records, $examineGist = null): Collection
	{
		$guardName = $examineGist ?? $this->name;
		
		// Get one instance of Permit or Role or User
		$firstRecord = $records->first();
		
		if ($firstRecord instanceof PermitContract or $firstRecord instanceof RoleContract) {
			return $records->map(function ($record) use ($guardName) {
				if ($record->guard === $guardName) {
					return $record;
				}
			});
		}
		
		/**
		 * Because user has no guard name,
		 * therefore check whether expected examineGist within right config('rbac.guard').
		 */
		if ($examineGist === $this->name and $firstRecord instanceof $this->providerClass) {
			return $records;
		}
		
		return collect();
	}
	
	public function __get($name)
	{
		return isset($this->$name) ? $this->$name : null;
	}
}
