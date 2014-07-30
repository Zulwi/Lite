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
 * Lite框架主类
 * Class Lite
 */
class Lite {

	/**
	 * 框架初始化
	 */
	public static function start() {
		register_shutdown_function('Lite::fatalError');
		set_error_handler('Lite::appError');
		set_exception_handler('Lite::appException');
		spl_autoload_register('Lite::autoload');
		require COMMON_PATH . 'function.php';
		setCharset();
		if (version_compare(PHP_VERSION, '5.4.0', '<')) {
			@ini_set('magic_quotes_runtime', 0);
			define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc() ? true : false);
		} else define('MAGIC_QUOTES_GPC', false);
		self::loadConfig();
		date_default_timezone_set(C('DEFAULT_TIMEZONE'));
		$app = new App();
		$app ->init();
	}

	/**
	 * 自动加载类库
	 * @param $classname 类名
	 */
	public static function autoload($classname) {
		$path = LIB_PATH . $classname . '.class.php';
		if (is_file($path)) {
		} elseif (endsWith($classname, 'Adapter')) {
			$path = LIB_PATH . 'Adapter/' . str_replace('Adapter', '', $classname) . '.adt.php';
		} elseif (endsWith($classname, 'Controller')) {
			$path = COMMON_GROUP . CONTROLLER_DIR . str_replace('Controller', C('CONTROLLER_EXT'), $classname);
			if (!is_file($path) && defined('GROUP_PATH')) $path = GROUP_PATH . CONTROLLER_DIR . str_replace('Controller', C('CONTROLLER_EXT'), $classname);
		} elseif (endsWith($classname, 'Model')) {
			$path = COMMON_GROUP . CONTROLLER_DIR . str_replace('Model', C('MODEL_EXT'), $classname);
			if (!is_file($path) && defined('GROUP_PATH')) $path = GROUP_PATH . MODEL_DIR . str_replace('Model', C('MODEL_EXT'), $classname);
		} else {
			$path = APP_LIB . $classname . C('CLASS_EXT');
		}
		if (is_file($path)) include $path;
	}

	/**
	 * 加载框架默认配置
	 */
	public static function loadConfig() {
		if (is_file(COMMON_PATH . CONFIG_FILE)) C(include(COMMON_PATH . CONFIG_FILE));
		if (is_file(COMMON_GROUP . CONFIG_DIR . CONFIG_FILE)) {
		}
		C(include(COMMON_GROUP . CONFIG_DIR . CONFIG_FILE));
		L(include LANG_PATH . strtolower(C('DEFAULT_LANG')) . '.php');
		$langPath = COMMON_GROUP . LANG_DIR . strtolower(C('DEFAULT_LANG')) . '.php';
		if (is_file($langPath)) L(include $langPath);
	}

	/**
	 * 致命错误回调方法
	 */
	public static function fatalError() {
		if ($e = error_get_last()) {
			switch ($e['type']) {
				case E_ERROR:
				case E_PARSE:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
				case E_USER_ERROR:
					ob_end_clean();
					self :: showError($e);
					break;
			}
		}
	}

	/**
	 * 普通错误回调方法
	 * @param $errno 错误代码
	 * @param $errstr 错误信息
	 * @param $errfile 错误文件
	 * @param $errline 错误行数
	 */
	public static function appError($errno, $errstr, $errfile, $errline) {
		switch ($errno) {
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				ob_end_clean();
				$errorStr = "$errstr " . $errfile . " 第 $errline 行.";
				self :: showError($errorStr);
				break;
		}
	}

	/**
	 * 普通异常回调方法
	 * @param $e 异常
	 */
	public static function appException($e) {
		$error = array();
		$error['message'] = $e ->getMessage();
		$trace = $e ->getTrace();
		if ('E'==$trace[0]['function']) {
			$error['file'] = $trace[0]['file'];
			$error['line'] = $trace[0]['line'];
		} else {
			$error['file'] = $e ->getFile();
			$error['line'] = $e ->getLine();
		}
		$error['trace'] = $e ->getTraceAsString();
		sendHttpStatus();
		self :: showError($error);
	}

	/**
	 * 错误打印方法
	 * @param $error 错误
	 */
	private static function showError($error) {
		$e = array();
		if (APP_DEBUG) {
			if (!is_array($error)) {
				$trace = debug_backtrace();
				$e['message'] = $error;
				$e['file'] = $trace[0]['file'];
				$e['line'] = $trace[0]['line'];
				ob_start();
				debug_print_backtrace();
				$e['trace'] = ob_get_clean();
			} else $e = $error;
		} else {
			$e['message'] = L('SYSTEM_ERROR');
			$e['tips'] = L('CONTACT_ADMIN');
		}
		include(LITE_PATH . 'Template/sys_error.php');
		exit;
	}
}
