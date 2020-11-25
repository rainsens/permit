<?php
namespace Rainsens\Rbac\Contracts;

interface PermitContract
{
	public static function create(string $permitName);
	
	public static function findByName(string $name);
	
	public static function findById(int $id);
	
	public function roleItems();
	
	public function users();
}
