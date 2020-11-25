<?php

function create($model, array $attributes = [], $times = null) {
	return factory($model, $times)->create($attributes);
}

function createPermit($attributes = [], $times = null) {
	return create(\Rainsens\Rbac\Models\Permit::class, $attributes, $times);
}

function createRole($attributes = [], $times = null) {
	return create(\Rainsens\Rbac\Models\Role::class, $attributes, $times);
}

function createUser($attributes = [], $times = null) {
	return create(\Rainsens\Rbac\Tests\Dummy\Models\User::class, $attributes, $times);
}
