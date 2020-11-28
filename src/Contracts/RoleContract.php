<?php
namespace Rainsens\Rbac\Contracts;

interface RoleContract
{
	public static function create(array $attributes);
	
	public static function findByName(string $name);
	
	public static function findById(int $id);
	
	public function permits();
	
	public function users();
}
