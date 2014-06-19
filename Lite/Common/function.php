<?php
if (!defined('LITE_PATH')) exit();

function C($key, $value = null, $default = null) {
	static $_config = array();
	if (empty($key)) return $_config;
	if (is_string($key)) {
		if (is_null($value)) return isset($_config[strtoupper($key)]) ? $_config[strtoupper($key)] : $default;
		$_config[strtoupper($key)] = $value;
	} elseif (is_array($key)) $_config = array_merge($_config, array_change_key_case($key, CASE_UPPER));
}

function L($name = null, $value = null) {
	static $_lang = array();
	if (empty($name)) return $_lang;
	if (is_string($name)) {
		$name = strtoupper($name);
		if (is_null($value)) return isset($_lang[$name]) ? $_lang[$name] : $name;
		$_lang[$name] = $value;
		return;
	} elseif (is_array($name)) $_lang = array_merge($_lang, array_change_key_case($name, CASE_UPPER));
}

function E($msg, $code = null) {
	throw new Exception($msg, $code);
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
	ob_end_clean();
	header('HTTP/1.1 404 Not Found');
	header('Status:404 Not Found');
}

function setCharset($charset = 'utf-8') {
	header("Content-type: text/html; charset=" . $charset);
}

function redirect($url) {
	ob_end_clean();
	header('Location:' . $url);
}

function dump($var) {
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
}

function toGUIDString($mix) {
	if (is_object($mix)) return spl_object_hash($mix);
	elseif (is_resource($mix)) $mix = get_resource_type($mix) . strval($mix);
	else $mix = serialize($mix);
	return md5($mix);
}
