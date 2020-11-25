<?php

return [
	
	/**
	 * Default auth guard name for this package.
	 * If you use package with only one guard
	 * you could put it here.
	 * But once you use more than one auth guard in your application
	 * you could change it at runtime.
	 * config(['rbac.guard' => 'guard_name'])
	 */
	'guard' => 'web',
	
	'models' => [
		
		'permit' => Rainsens\Rbac\Models\Permit::class,
		
		'role' => Rainsens\Rbac\Models\Role::class,
	],
	'tables' => [
		
		'roles' => 'roles',
		
		'permits' => 'permits',
		
		'permit_roles' => 'permit_roles',
		
		'permit_users' => 'permit_users',
		
		'role_users' => 'role_users',
	],
	'columns' => [
		'permit_morph_id'       => 'permit_id',
		'permit_morph_name'     => 'permitable',
		'permit_morph_key'      => 'permitable_id',
		'permit_morph_type'     => 'permitable_type',
		
		'role_morph_id'         => 'role_id',
		'role_morph_name'       => 'rolable',
		'role_morph_key'        => 'rolable_id',
		'role_morph_type'       => 'rolable_type',
	]
];
