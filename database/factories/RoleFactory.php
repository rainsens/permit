<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Rainsens\Rbac\Models\Role;

$factory->define(Role::class, function (Faker $faker) {
	return [
		'slug' => $faker->uuid,
		'name' => 'Manager',
		'guard' => 'web'
	];
});
