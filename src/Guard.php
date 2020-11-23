<?php

namespace Rainsens\Permit;

use Illuminate\Support\Collection;

class Guard
{
    /**
     * The field guard_name in model Role, Permit, User.
     * return collection of (guard_name) property if exist on class or object
     * otherwise will return collection of guards names that exists in config/auth.php.
     */
    public static function getNames($model): Collection
    {
    	// 1. get guard_name
        if (is_object($model)) {
            if (method_exists($model, 'guardName')) {
                $guardName = $model->guardName();
            } else {
                $guardName = $model->guard_name ?? null;
            }
        }

        // 2. if it does not exist, get it from class.
        if (! isset($guardName)) {
            $class = is_object($model) ? get_class($model) : $model;

            $guardName = (new \ReflectionClass($class))->getDefaultProperties()['guard_name'] ?? null;
        }

        // 3. if get guard name return it.
        if ($guardName) {
            return collect($guardName);
        }

        // 4. if does not get, return the default guard name from config/auth.php.
        return collect(config('auth.guards'))
            ->map(function ($guard) {
                if (! isset($guard['provider'])) {
                    return;
                }

                return config("auth.providers.{$guard['provider']}.model");
            })
            ->filter(function ($model) use ($class) {
                return $class === $model;
            })
            ->keys();
    }
	
	/**
	 * The default guard name of Laravel in config/auth.php
	 * @param $class
	 * @return string
	 */
    public static function getDefaultName($class): string
    {
        $default = config('auth.defaults.guard');

        return static::getNames($class)->first() ?: $default;
    }
}
