<?php
if (!defined('LITE_PATH')) exit();

class App {
	public static function init() {
		define('IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')])) ? true : false);
		self :: loadConfig();
		self :: dispatch();
	}

	public static function loadConfig() {
		if (is_file(COMMON_PATH . 'config.php')) C(include(COMMON_PATH . 'config.php'));
		if (is_file(APP_COMMON . 'config.php')) C(include(APP_COMMON . 'config.php'));
		$extraConfig = C('EXT_CONFIG');
		if (is_string($extraConfig) && !empty($extraConfig)) $extraConfig = explode(',', $extraConfig);
		if (is_array($extraConfig)) {
			foreach($extraConfig as $config) {
				if (is_file(APP_COMMON . $config . '.php')) C(include(APP_COMMON . $config . '.php'));
			}
		}
	}

	public static function dispatch() {
		$varPath = C('VAR_PATHINFO', null, 's');
		$varAddon = C('VAR_ADDON', null, 'e');
		$varModule = C('VAR_MODULE', null, 'm');
		$varAction = C('VAR_ACTION', null, 'a');
		if (isset($_GET[$varPath])) {
			$_SERVER['PATH_INFO'] = $_GET[$varPath];
			unset($_GET[$varPath]);
		}
		if (isset($_SERVER['PATH_INFO'])) {
			$param = explode('/', $_SERVER['PATH_INFO']);
			//if(preg_match)
			//if() TO-DO
		}
		$module = isset($_GET[$varModule]) ? $_GET[$varModule] : 'Index';
		$action = isset($_GET[$varAction]) ? $_GET[$varAction] : 'Index';

		if (empty($module)) {
		}
		define('__DOMAIN__', $_SERVER['HTTP_HOST']);
		define('__MODULE__', htmlspecialchars(urldecode($_GET[$varModule])));
		define('__ACTION__', htmlspecialchars(urldecode($_GET[$actionName])));
	}
}
