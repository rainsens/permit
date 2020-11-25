<?php

return [
	
	/**
	 * Default auth guard name for this package.
	 * If you use package with only one guard
	 * you could put it here.
	 * But once you use more than one auth guard in your application
	 * you could change it at runtime.
	 */
	'guard' => 'web',
	
	'models' => [
		
		'permit' => Rainsens\Authorize\Models\Permit::class,
		
		'role' => Rainsens\Authorize\Models\Role::class,
	],
	'table_names' => [
		
		'roles' => 'roles',
		
		'permits' => 'permits',
		
		'permit_roles' => 'permit_roles',
		
		'permit_users' => 'permit_users',
		
		'role_users' => 'role_users',
	]
];
