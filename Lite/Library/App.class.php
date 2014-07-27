<?php
if (!defined('LITE_PATH')) exit();

class App {
	const URL_MODEL_NORMAL = 0;
	const URL_MODEL_PATHINFO = 1;
	const URL_MODEL_REWRITE = 2;
	const URL_MODEL_SPECIAL = 3;

	public function init() {
		define('IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest') || !empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT', null, 'ajax')])) ? true : false);
		$this ->dispatch();
		$this ->loadConfig();
		$classname = __CONTROLLER__ . 'Controller';
		$method = __ACTION__;
		if (!is_dir(GROUP_PATH)) E(L('GROUP_NOT_EXIST') . ' : ' . __GROUP__);
		if (!is_file(GROUP_PATH . CONTROLLER_DIR . str_replace('Controller', C('CONTROLLER_EXT', null, '.controller.php'), $classname))) E(L('CONTROLLER_NOT_EXIST') . ' : ' . __CONTROLLER__);
		$controller = new $classname();
		$controller ->$method();
	}

	public function loadConfig() {
		if (is_file(GROUP_PATH . 'Commom/config.php')) C(include(GROUP_PATH . 'Commom/config.php'));
		$extraConfig = C('EXT_CONFIG');
		if (is_string($extraConfig) && !empty($extraConfig)) $extraConfig = explode(',', $extraConfig);
		if (is_array($extraConfig)) {
			foreach ($extraConfig as $config) {
				$extraPath = GROUP_PATH . 'Common/' . $config . '.php';
				if (is_file($extraPath)) C(include($extraPath));
			}
		}
		$langPath = GROUP_PATH . LANG_DIR . strtolower(C('DEFAULT_LANG', null, 'zh-cn')) . '.php';
		if (is_file($langPath)) L(include $langPath);
	}

	public function __destruct() {
	}

	public function dispatch() {
		$var = array('path' => C('VAR_PATHINFO', null, 's'), 'group' => C('VAR_GROUP', null, 'g'), 'controller' => C('VAR_CONTROLLER', null, 'c'), 'action' => C('VAR_ACTION', null, 'a'), 'param' => C('VAR_PARAM', null, 'p'));
		$urlModel = C('URL_MODEL', null, 0);
		$group = $controller = $action = $param = '';
		$pathinfo = !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
		if ($urlModel!=self :: URL_MODEL_NORMAL) {
			$pathinfo = isset($_GET[$var['path']]) ? $_GET[$var['path']] : $pathinfo;
			unset($_GET[$var['path']]);
		}
		if (startsWith($pathinfo, '/')) $pathinfo = substr($pathinfo, 1);
		switch ($urlModel) {
			case self :: URL_MODEL_PATHINFO:
			case self :: URL_MODEL_SPECIAL:
			case self :: URL_MODEL_REWRITE:
				$matches = array();
				if (preg_match_all('#(\w+)/(\w+)#', $pathinfo, $matches)) {
					if (count($matches)==3) {
						foreach ($matches[1] as $key => $value) {
							$_GET[$value] = $matches[2][$key];
						}
					}
				}
				if (!empty($pathinfo)) $_GET[0] = explode('/', $pathinfo);
			case self :: URL_MODEL_NORMAL:
				$group = isset($_GET[$var['group']]) ? $_GET[$var['group']] : (isset($_GET[0][0]) ? $_GET[0][0] : C('DEFAULT_GROUP', null, 'Home'));
				$controller = isset($_GET[$var['controller']]) ? $_GET[$var['controller']] : (isset($_GET[0][1]) ? $_GET[0][1] : C('DEFAULT_CONTROLLER', null, 'Index'));
				$action = isset($_GET[$var['action']]) ? $_GET[$var['action']] : (isset($_GET[0][2]) ? $_GET[0][2] : C('DEFAULT_ACTION', null, 'Index'));
				$param = isset($_GET[$var['param']]) ? $_GET[$var['param']] : (isset($_GET[0][3]) ? $_GET[0][3] : C('DEFAULT_PARAM', null, ''));
				break;
		}
		define('__DOMAIN__', $_SERVER['HTTP_HOST']);
		define('__GROUP__', htmlspecialchars(urldecode($group)));
		define('__CONTROLLER__', htmlspecialchars(urldecode($controller)));
		define('__ACTION__', htmlspecialchars(urldecode($action)));
		define('__PARAM__', htmlspecialchars(urldecode($param)));
		define('GROUP_PATH', APP_PATH . __GROUP__ . '/');
		$_GET[$var['controller']] = __CONTROLLER__;
		$_GET[$var['action']] = __ACTION__;
		$_GET[$var['param']] = __PARAM__;
	}
}
