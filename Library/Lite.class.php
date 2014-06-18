<?php
if (!defined('LITE_PATH')) exit();

class Lite {
	const controllerExt = '.controller.php';
	const modelExt = '.model.php';

	public static function start() {
		require COMMON_PATH . 'function.php';
		if (version_compare(PHP_VERSION, '5.4.0', '<')) {
			ini_set('magic_quotes_runtime', 0);
			define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc()? true : false);
		} else define('MAGIC_QUOTES_GPC', false);
		spl_autoload_register('Lite::autoload');
		register_shutdown_function('Lite::fatalError');
		set_error_handler('Lite::appError');
		set_exception_handler('Lite::appException');
	}

	public static function autoload($classname) {
		if (is_file(LIB_PATH . $classname . '.class.php')) include LIB_PATH . $classname . '.class.php';
		elseif (endsWith($classname, 'Controller')) {
			$path = CONTROLLER_PATH . str_replace('Controller', self :: controllerExt, $classname);
			if (is_file($path)) include $path;
		} elseif (endsWith($classname, 'Model')) {
			$path = MODEL_PATH . str_replace('Model', self :: modelExt, $classname);
			if (is_file($path)) include $path;
		}
	}

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

	public static function appException($e) {
		$error = array();
		$error['message'] = $e -> getMessage();
		$trace = $e -> getTrace();
		if ('E' == $trace[0]['function']) {
			$error['file'] = $trace[0]['file'];
			$error['line'] = $trace[0]['line'];
		} else {
			$error['file'] = $e -> getFile();
			$error['line'] = $e -> getLine();
		}
		$error['trace'] = $e -> getTraceAsString();
		ob_end_clean();
		sendHttpError();
		self :: showError($error);
	}

	public static function showError($error) {
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
			$e['message'] = '系统错误';
		}
		include(LITE_PATH . 'Template/error.php');
		exit;
	}
}
