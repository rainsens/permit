<?php
namespace Rainsens\Rbac\Contracts;

interface PermitContract
{
	public static function create(string $name, string $path = null, string $method = null);
	
	public static function findByName(string $name);
	
	public static function findById(int $id);
	
	public static function findByPath(string $path, string $method = null);
	
	public function roles();
	
	public function users();
}
