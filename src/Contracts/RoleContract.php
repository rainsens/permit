<?php
namespace Rainsens\Authorize\Contracts;

interface RoleContract
{
	public static function create(string $roleName);
	
	public static function findByName(string $name);
	
	public static function findById(int $id);
	
	public function permitItems();
	
	public function users();
}
