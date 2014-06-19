<?php
if (!defined('LITE_PATH')) exit();

class Lite {
	private static $classExt;
	public static function start() {
		register_shutdown_function('Lite::fatalError');
		set_error_handler('Lite::appError');
		set_exception_handler('Lite::appException');
		spl_autoload_register('Lite::autoload');
		require COMMON_PATH . 'function.php';
		if (version_compare(PHP_VERSION, '5.4.0', '<')) {
			ini_set('magic_quotes_runtime', 0);
			define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc()? true : false);
		} else define('MAGIC_QUOTES_GPC', false);
		App :: init();
	}

	public static function autoload($classname) {
		$path = LIB_PATH . $classname . '.class.php';
		if (is_file($path)) {
		} elseif (endsWith($classname, 'Adapter')) {
			$path = LIB_PATH . 'Adapter/' . str_replace('Adapter', '', $classname) . '.adapter.php';
		} elseif (endsWith($classname, 'Controller')) {
			$path = APP_CONTROLLER . str_replace('Controller', C('CONTROLLER_EXT', null, '.controller.php'), $classname);
		} elseif (endsWith($classname, 'Model')) {
			$path = APP_MODEL . str_replace('Model', C('MODEL_EXT', null, '.model.php'), $classname);
		} else {
			$path = APP_LIB . $classname . C('CLASS_EXT');
		}
		if (is_file($path)) include $path;
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
		sendHttpError();
		self :: showError($error);
	}

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
			$e['message'] = '系统错误';
			$e['tips'] = '您可以联系管理员以便更好地完善它';
		}
		include(LITE_PATH . 'Template/error.php');
		exit;
	}
}
