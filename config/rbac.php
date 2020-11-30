<?php

return [
	
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
