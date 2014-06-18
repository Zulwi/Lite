<?php
if (!defined('LITE_PATH')) exit();

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
