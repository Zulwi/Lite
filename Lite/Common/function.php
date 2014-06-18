<?php
if (!defined('LITE_PATH')) exit();

function C($key, $value = null, $default = null) {
	static $_config = array();
	if (empty($key)) return $_config;
	if (is_string($key)) {
		if (is_null($value)) return isset($_config[strtoupper($key)]) ? $_config[strtoupper($key)] : $default;
		$_config[strtoupper($key)] = $value;
	} elseif (is_array($key)) $_config = array_merge($_config, array_change_key_case($name, CASE_UPPER));
}

function startsWith($str, $prefix) {
	if (substr($str, 0, strlen($prefix)) == $prefix)return true;
	return false;
}

function endsWith($str, $suffix) {
	if (substr($str, - strlen($suffix)) == $suffix)return true;
	return false;
}

function sendHttpError() {
	header('HTTP/1.1 404 Not Found');
	header('Status:404 Not Found');
}

function redirect($url) {
	header('Location:' . $url);
}
