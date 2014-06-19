<?php
if (!defined('LITE_PATH')) exit();

class App {
	const URL_MODEL_NORMAL = 0;
	const URL_MODEL_PATHINFO = 1;
	const URL_MODEL_REWRITE = 2;
	const URL_MODEL_SPECIAL = 3;
	public function init() {
		define('IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')])) ? true : false);
		$this -> loadConfig();
		$this -> dispatch();
	}

	public function loadConfig() {
		if (is_file(COMMON_PATH . 'config.php')) C(include(COMMON_PATH . 'config.php'));
		if (is_file(APP_COMMON . 'config.php')) C(include(APP_COMMON . 'config.php'));
		$extraConfig = C('EXT_CONFIG');
		if (is_string($extraConfig) && !empty($extraConfig)) $extraConfig = explode(',', $extraConfig);
		if (is_array($extraConfig)) {
			foreach($extraConfig as $config) {
				if (is_file(APP_COMMON . $config . '.php')) C(include(APP_COMMON . $config . '.php'));
			}
		}
		L(include LANG_PATH . strtolower(C('DEFAULT_LANG', null, 'zh-cn')) . '.php');
		$path = APP_LANG . strtolower(C('DEFAULT_LANG', null, 'zh-cn')) . '.php';
		if (is_file($path)) L(include $path);
	}

	public function __destruct() {
	}

	public function dispatch() {
		$var = array('path' => C('VAR_PATHINFO', null, 's'),
			'controller' => C('VAR_CONTROLLER', null, 'c'),
			'action' => C('VAR_ACTION', null, 'a'),
			'param' => C('VAR_PARAM', null, 'p'),
			);
		$urlModel = C('URL_MODEL', null, 0);
		$controller = $action = $param = '';
		if ($urlModel != self :: URL_MODEL_NORMAL) {
			$_SERVER['PATH_INFO'] = isset($_GET[$var['path']]) ? $_GET[$var['path']] : '';
			unset($_GET[$var['path']]);
		}
		if (startsWith($_SERVER['PATH_INFO'], '/')) $_SERVER['PATH_INFO'] = substr($_SERVER['PATH_INFO'], 1);
		switch ($urlModel) {
			case self :: URL_MODEL_PATHINFO:
			case self :: URL_MODEL_SPECIAL:
			case self :: URL_MODEL_REWRITE:
				$matches = array();
				if (preg_match_all('#(\w+)/(\w+)#', $_SERVER['PATH_INFO'], $matches)) {
					if (count($matches) == 3) {
						foreach($matches[1] as $key => $value) {
							$_GET[$value] = $matches[2][$key];
						}
					}
				}
				if(!empty($_SERVER['PATH_INFO'])) $_GET[0] = explode('/', $_SERVER['PATH_INFO']);
			case self :: URL_MODEL_NORMAL:
				$controller = isset($_GET[$var['controller']]) ? $_GET[$var['controller']] : (isset($_GET[0][0]) ? $_GET[0][0] : C('DEFAULT_CONTROLLER', null, 'Index'));
				$action = isset($_GET[$var['action']]) ? $_GET[$var['action']] : (isset($_GET[0][1]) ? $_GET[0][1] : C('DEFAULT_ACTION', null, 'Index'));
				$param = isset($_GET[$var['param']]) ? $_GET[$var['param']] : (isset($_GET[0][2]) ? $_GET[0][2] : C('DEFAULT_PARAM', null, ''));
				break;
		}
		define('__DOMAIN__', $_SERVER['HTTP_HOST']);
		define('__MODULE__', htmlspecialchars(urldecode($controller)));
		define('__ACTION__', htmlspecialchars(urldecode($action)));
		define('__PARAM__', htmlspecialchars(urldecode($param)));
		$_GET[$var['controller']] = __MODULE__;
		$_GET[$var['action']] = __ACTION__;
		$_GET[$var['param']] = __PARAM__;
		$classname = __MODULE__ . 'Controller';
		$method = __ACTION__;
		if (!is_file(APP_CONTROLLER . str_replace('Controller', C('CONTROLLER_EXT', null, '.controller.php'), $classname))) E(L('CONTROLLER_NOT_EXIST') . ':' . __MODULE__);
		$controller = new $classname();
		$controller -> $method();
	}
}
