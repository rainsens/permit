<?php
namespace Rainsens\Rbac\Contracts;

interface PermitContract
{
	const HTTP_METHODS = [
		'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'
	];
	
	public static function create(array $attributes);
	
	public static function findByName(string $name);
	
	public static function findById(int $id);
	
	public function roles();
	
	public function users();
}
