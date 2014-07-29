<?php
/**
 * Copyright (c) 2010-2014 Zulwi Studio All Rights Reserved.
 * Author  @JerryLocke
 * Date    2014/7/27
 * Blog    http://Jerry.hk
 * Email   i@Jerry.hk
 * Team    http://www.zhuwei.cc
 */

if (!defined('LITE_PATH')) exit;

/**
 * 配置函数
 * @param $key 配置名
 * @param null $value 配置值
 * @param null $default 默认值
 * @return array|null 结果
 */
function C($key, $value = null, $default = null) {
	static $_config = array();
	if (empty($key)) return $_config;
	if (is_string($key)) {
		if (is_null($value)) return isset($_config[strtoupper($key)]) ? $_config[strtoupper($key)] : $default;
		$_config[strtoupper($key)] = $value;
	} elseif (is_array($key)) $_config = array_merge($_config, array_change_key_case($key, CASE_UPPER));
}

/**
 * 语言函数
 * @param null $name 语言名
 * @param null $value 语言值
 * @return array|string 结果
 */
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

/**
 * 抛出错误
 * @param $msg 错误信息
 * @param null $code 错误代码
 * @throws Exception 抛出异常
 */
function E($msg, $code = null) {
	throw new Exception($msg, $code);
}

/**
 * 检查字符串是否以另一字符串开始
 * @param $str 要检查的字符串
 * @param $prefix 开始的字符串
 * @return bool 结果
 */
function startsWith($str, $prefix) {
	if (substr($str, 0, strlen($prefix))==$prefix) return true;
	return false;
}

/**
 * 检查字符串是否以另一字符串结束
 * @param $str 要检查的字符串
 * @param $suffix 结束的字符串
 * @return bool 结果
 */
function endsWith($str, $suffix) {
	if (substr($str, -strlen($suffix))==$suffix) return true;
	return false;
}

/**
 * 发送HTTP状态码
 * @param $code 状态码
 */
function sendHttpStatus($code, $clean = fase) {
	static $_status = array( // Informational 1xx
		100 => 'Continue', 101 => 'Switching Protocols', // Success 2xx
		200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', // Redirection 3xx
		300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Moved Temporarily ', // 1.1
		303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', // 306 is deprecated but reserved
		307 => 'Temporary Redirect', // Client Error 4xx
		400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed', // Server Error 5xx
		500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported', 509 => 'Bandwidth Limit Exceeded');
	if (isset($_status[$code])) {
		if ($clean) ob_end_clean();
		header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
		// 确保FastCGI模式下正常
		header('Status:' . $code . ' ' . $_status[$code]);
	}
}

/**
 * 设置编码
 * @param string $charset 编码
 */
function setCharset($charset = 'utf-8') {
	header("Content-type: text/html; charset=" . $charset);
}

/**
 * 重定向
 * @param $url 要重定向的URL
 */
function redirect($url) {
	ob_end_clean();
	header('Location:' . $url);
}

/**
 * 抛出对浏览器友好的变量
 * @param $var 变量
 * @param $exit 是否终止程序运行
 */
function dump($var, $exit) {
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
	if ($exit) exit();
}

/**
 * 生成唯一识别码
 * @param $mix 要生成的变量/对象/资源
 * @return string 唯一识别码
 */
function toGUIDString($mix) {
	if (is_object($mix)) return spl_object_hash($mix); elseif (is_resource($mix)) $mix = get_resource_type($mix) . strval($mix);
	else $mix = serialize($mix);
	return md5($mix);
}

/**
 * 缩略字符串
 * @param $string 要切割的字符串
 * @param $length 缩略长度
 * @param string $dot 超出长度的内容的代替内容
 * @return mixed|string 缩略后的字符串
 */
function cutStr($string, $length, $dot = '...') {
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

/**
 * 从HTML源码中取得图片路径
 * @param $content HTML代码
 * @return null 图片路径
 */
function getImgSrc($content) {
	$regx = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
	preg_match_all($regx, $content, $matches);
	return isset($matches[1][0]) ? $matches[1][0] : null;
}

/**
 * 过滤文本
 * @param $str 要过滤的文本
 * @return string 过滤后的文本
 */
function wrapText($str) {
	$str = trim($str);
	$str = str_replace("\t", '', $str);
	$str = str_replace("\r", '', $str);
	$str = str_replace("\n", '', $str);
	$str = str_replace(' ', '', $str);
	return trim($str);
}

/**
 * 检查字符串是否合法
 * @param $type 检查类型：email、password
 * @param $string 要检查的字符串
 * @return int 结果
 */
function checkString($type, $string) {
	switch ($type) {
		case 'email':
			return preg_match('/^[A-z0-9._-]+@[A-z0-9._-]+\.[A-z0-9._-]+$/', $email);
		case 'password':
			return preg_match('/^[\w~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]{8,16}$/', $password);
	}
}

/**
 * 生成随机字符串
 * @param int $length 长度
 * @param int $type 类型：-1为特殊字符+英文大小写+数字组合，0为英文大小写+数字组合，1为数字，2为英文小写，3为英文大写，4为特殊字符
 * @return string 随机生成的字符串
 */
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

/**
 * 获取客户端IP地址
 * @param int $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param bool $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function getIp($type = 0, $adv = false) {
	$type = $type ? 1 : 0;
	static $ip = NULL;
	if ($ip!==NULL) return $ip[$type];
	if ($adv) {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$pos = array_search('unknown', $arr);
			if (false!==$pos) unset($arr[$pos]);
			$ip = trim($arr[0]);
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	} elseif (isset($_SERVER['REMOTE_ADDR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	// IP地址合法验证
	$long = sprintf("%u", ip2long($ip));
	$ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
	return $ip[$type];
}

function escapeString($string, $force = 0, $strip = false) {
	if (!MAGIC_QUOTES_GPC || $force) {
		if (is_array($string)) {
			foreach ($string as $key => $val) {
				$string[$key] = escapeString($val, $force, $strip);
			}
		} else {
			$string = addslashes($strip ? stripslashes($string) : $string);
		}
	}
	return $string;
}