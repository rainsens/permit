<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Rainsens\Rbac\Models\Permit;

$factory->define(Permit::class, function (Faker $faker) {
	return [
		'slug' => 'articles.*',
		'name' => 'Edit Articles',
		'path' => '/',
		'guard' => 'web'
	];
});
