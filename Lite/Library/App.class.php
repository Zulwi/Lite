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
 * 项目类
 * Class App
 */
class App {
	/**
	 * 普通URL模式
	 */
	const URL_MODE_NORMAL = 0;
	/**
	 * PATHINFO URL模式
	 */
	const URL_MODE_PATHINFO = 1;
	/**
	 * 伪静态URL模式
	 */
	const URL_MODE_REWRITE = 2;
	/**
	 * 兼容URL模式
	 */
	const URL_MODE_SPECIAL = 3;

	/**
	 * 项目初始化
	 */
	public function init() {
		$this ->dispatch();
		$this ->loadConfig();
		define('IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest') || !empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')])) ? true : false);
		$classname = __CONTROLLER__ . 'Controller';
		$method = __ACTION__;
		if (!is_dir(GROUP_PATH)) E(L('GROUP_NOT_EXIST') . ' : ' . __GROUP__);
		if (!is_file(GROUP_PATH . CONTROLLER_DIR . str_replace('Controller', C('CONTROLLER_EXT'), $classname))) E(L('CONTROLLER_NOT_EXIST') . ' : ' . __CONTROLLER__);
		$controller = new $classname();
		$controller ->$method();
	}

	/**
	 * 加载项目配置
	 */
	public function loadConfig() {
		if (is_file(GROUP_PATH . CONFIG_DIR . CONFIG_FILE)) C(include(GROUP_PATH . CONFIG_DIR . CONFIG_FILE));
		$extraConfig = C('EXT_CONFIG');
		if (is_string($extraConfig) && !empty($extraConfig)) $extraConfig = explode(',', $extraConfig);
		if (is_array($extraConfig)) {
			foreach ($extraConfig as $config) {
				$extraPath = GROUP_PATH . CONFIG_DIR . $config . '.php';
				if (is_file($extraPath)) C(include($extraPath));
			}
		}
		$functionPath = GROUP_PATH . CONFIG_DIR . FUNCTION_FILE;
		if (is_file($functionPath)) include($functionPath);
		$langPath = GROUP_PATH . LANG_DIR . strtolower(C('DEFAULT_LANG')) . '.php';
		if (is_file($langPath)) L(include $langPath);
	}

	/**
	 * 析构方法
	 */
	public function __destruct() {
	}

	/**
	 * 路由分发
	 */
	public function dispatch() {
		$var = array('path' => C('VAR_PATHINFO'), 'group' => C('VAR_GROUP'), 'controller' => C('VAR_CONTROLLER'), 'action' => C('VAR_ACTION'));
		$urlModel = C('URL_MODEL');
		$group = $controller = $action = '';
		$pathinfo = !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
		if ($urlModel!=self :: URL_MODE_NORMAL) {
			$pathinfo = isset($_GET[$var['path']]) ? $_GET[$var['path']] : $pathinfo;
			unset($_GET[$var['path']]);
		}
		if (startsWith($pathinfo, '/')) $pathinfo = substr($pathinfo, 1);
		switch ($urlModel) {
			case self :: URL_MODE_PATHINFO:
			case self :: URL_MODE_SPECIAL:
			case self :: URL_MODE_REWRITE:
				$virgule = substr_count($pathinfo, '/');
				if ($virgule>2) {
					$pos = strpos($pathinfo, '?');
					if ($pos) {
						$param = explode('&', substr($pathinfo, $pos));
						foreach ($param as $slice) {
							$slice = explode('=', $slice);
							if (count(array_filter($slice))==2) $_GET[$slice[0]] = $slice[1];
						}
					} else {
						for ($i = 3; $i<$virgule; $i += 2) {
							$first = getStrPosByCount($pathinfo, '/', $i)+1;
							$last = getStrPosByCount($pathinfo, '/', $i+2)-$first-1;
							$param = explode('/', substr($pathinfo, $first, $last>=0 ? $last : -1));
							if (count(array_filter($param))==2) $_GET[$param[0]] = $param[1];
						}
					}
				}
				if (!empty($pathinfo)) $_GET[0] = explode('/', $pathinfo);
			case self :: URL_MODE_NORMAL:
				$group = isset($_GET[$var['group']]) ? $_GET[$var['group']] : (isset($_GET[0][0]) ? $_GET[0][0] : C('DEFAULT_GROUP'));
				$controller = isset($_GET[$var['controller']]) ? $_GET[$var['controller']] : (isset($_GET[0][1]) ? $_GET[0][1] : C('DEFAULT_CONTROLLER'));
				$action = isset($_GET[$var['action']]) ? $_GET[$var['action']] : (isset($_GET[0][2]) ? $_GET[0][2] : C('DEFAULT_ACTION'));
				break;
		}
		define('__DOMAIN__', $_SERVER['HTTP_HOST']);
		define('__GROUP__', htmlspecialchars(urldecode($group)));
		define('__CONTROLLER__', htmlspecialchars(urldecode($controller)));
		define('__ACTION__', htmlspecialchars(urldecode($action)));
		define('GROUP_PATH', APP_PATH . __GROUP__ . '/');
		$_GET[$var['group']] = __GROUP__;
		$_GET[$var['controller']] = __CONTROLLER__;
		$_GET[$var['action']] = __ACTION__;
	}
}
