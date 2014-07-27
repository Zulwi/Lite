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
	if (substr($str, 0, strlen($prefix))==$prefix) return true;
	return false;
}

function endsWith($str, $suffix) {
	if (substr($str, -strlen($suffix))==$suffix) return true;
	return false;
}

function sendHttpStatus($code) {
	static $_status = array( // Informational 1xx
		100 => 'Continue', 101 => 'Switching Protocols',

		// Success 2xx
		200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content',

		// Redirection 3xx
		300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', // 1.1
		303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', // 306 is deprecated but reserved
		307 => 'Temporary Redirect',

		// Client Error 4xx
		400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed',

		// Server Error 5xx
		500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported', 509 => 'Bandwidth Limit Exceeded');
	if (isset($_status[$code])) {
		ob_end_clean();
		header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
	}
}

function setCharset($charset = 'utf-8') {
	header("Content-type: text/html; charset=" . $charset);
}

function redirect($url) {
	ob_end_clean();
	header('Location:' . $url);
}

function dump($var, $exit) {
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
	if ($exit) exit();
}

function toGUIDString($mix) {
	if (is_object($mix)) return spl_object_hash($mix); elseif (is_resource($mix)) $mix = get_resource_type($mix) . strval($mix);
	else $mix = serialize($mix);
	return md5($mix);
}

function cutStr($string, $length, $dot = ' ...') {
	if (strlen($string)<=$length) return $string;
	$pre = chr(1);
	$end = chr(1);
	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), $string);
	$strcut = '';
	$n = $tn = $noc = 0;
	while ($n<strlen($string)) {
		$t = ord($string[$n]);
		if ($t==9 || $t==10 || (32<=$t && $t<=126)) {
			$tn = 1;
			$n++;
			$noc++;
		} elseif (194<=$t && $t<=223) {
			$tn = 2;
			$n += 2;
			$noc += 2;
		} elseif (224<=$t && $t<=239) {
			$tn = 3;
			$n += 3;
			$noc += 2;
		} elseif (240<=$t && $t<=247) {
			$tn = 4;
			$n += 4;
			$noc += 2;
		} elseif (248<=$t && $t<=251) {
			$tn = 5;
			$n += 5;
			$noc += 2;
		} elseif ($t==252 || $t==253) {
			$tn = 6;
			$n += 6;
			$noc += 2;
		} else {
			$n++;
		}
		if ($noc>=$length) break;
	}
	if ($noc>$length) $n -= $tn;
	$strcut = substr($string, 0, $n);
	$strcut = str_replace(array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
	$pos = strrpos($strcut, chr(1));
	if ($pos!==false) $strcut = substr($strcut, 0, $pos);
	return $strcut . $dot;
}

function getImgSrc($content) {
	$regx = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
	preg_match_all($regx, $content, $matches);
	return isset($matches[1][0]) ? $matches[1][0] : null;
}

function wrapText($str) {
	$str = trim($str);
	$str = str_replace("\t", '', $str);
	$str = str_replace("\r", '', $str);
	$str = str_replace("\n", '', $str);
	$str = str_replace(' ', '', $str);
	return trim($str);
}

function checkString($type, $string) {
	switch ($type) {
		case 'email':
			return preg_match('/^[A-z0-9._-]+@[A-z0-9._-]+\.[A-z0-9._-]+$/', $email);
		case 'password':
			return preg_match('/^[\w~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]{8,16}$/', $password);
	}
}

function randCode($length = 5, $type = 0) {
	$arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|");
	if ($type==0) {
		array_pop($arr);
		$string = implode("", $arr);
	} elseif ($type=="-1") {
		$string = implode("", $arr);
	} else {
		$string = $arr[$type];
	}
	$count = strlen($string)-1;
	$code = '';
	for ($i = 0; $i<$length; $i++) {
		$code .= $string[rand(0, $count)];
	}
	return $code;
}