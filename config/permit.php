<?php

return [
	'table_names' => [
		
		/*
		 * When using the "HasRoles" trait from this package, we need to know which
		 * table should be used to retrieve your roles. We have chosen a basic
		 * default value but you may easily change it to any table you like.
		 */
		
		'roles' => 'roles',
		
		/*
		 * When using the "HasPermissions" trait from this package, we need to know which
		 * table should be used to retrieve your permissions. We have chosen a basic
		 * default value but you may easily change it to any table you like.
		 */
		
		'permits' => 'permits',
		
		/*
		 * When using the "HasRoles" trait from this package, we need to know which
		 * table should be used to retrieve your roles permissions. We have chosen a
		 * basic default value but you may easily change it to any table you like.
		 */
		
		'role_permits' => 'role_permits',
	],
];
