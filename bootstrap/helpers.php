<?php

if (! function_exists('_base_path')) {
	/**
	 * Get the path to adm folder of vendor.
	 * @param string $path
	 * @return string
	 */
	function _base_path($path = '') {
		return __DIR__ . '/../' . $path;
	}
}
