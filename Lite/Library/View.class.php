<?php
/**
 * Copyright (c) 2010-2014 Zulwi Studio All Rights Reserved.
 * Author  JerryLocke
 * DATE    2014/7/27
 * Blog    http://Jerry.hk
 * Email   i@Jerry.hk
 */

if(! defined('LITE_PATH')) exit;

/**
 * 视图类
 * Class View
 */
class View {
	/**
	 * @var 关联控制器实例
	 */
	private $controller;

	/**
	 * 构造方法
	 * @param $controller 关联控制器
	 */
	public function __construct($controller) {
		$this -> controller = $controller;
	}

	/**
	 * 显示内容
	 * @param string $template 模板名
	 * @param string $content 内容
	 */
	public function display($template = '', $content = '') {
		if (empty($template)) {
			if (empty($content)) {
				$this -> parseView($this -> parseViewFile(__ACTION__));
			} else {
				setCharset();
				echo $content;
			}
		} else {
			$this -> parseView($this -> parseViewFile($template));
		}
	}

	/**
	 * 解析模板路径
	 * @param $template 模板名
	 * @return bool|string 结果
	 */
	public function parseViewFile($template) {
		$path = APP_VIEW . $this -> controller . '/' . $template . '.' . C('VIEW_EXT', null, 'php');
		if (!is_file($path)) return false;
		return $path;
	}

	/**
	 * 解析模板
	 * @param $path 模板路径
	 */
	private function parseView($path) {
		if (!is_file($path)) E(L('TEMPLATE_NOT_EXIST'));
		include $path;
	}
}
