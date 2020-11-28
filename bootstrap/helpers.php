<?php

if (! function_exists('rbac_base_path')) {
	/**
	 * Get the path to adm folder of vendor.
	 * @param string $path
	 * @return string
	 */
	function rbac_base_path($path = '') {
		return __DIR__ . '/../' . $path;
	}
}
