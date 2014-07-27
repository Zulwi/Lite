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
 * 控制器抽象类
 * Class Db
 */
abstract class Controller {
	/**
	 * @var View 视图实例成员
	 */
	private $view;

	/**
	 * 构造一个视图实例
	 */
	public function __construct() {
		$this -> view = new View(__MODULE__);
		if(method_exists($this, '_init')) $this -> _init();
	}

	/**
	 * 显示模板
	 * @param string $template 模板名
	 * @param string $content 内容（仅为 show 方法调用）
	 */
	public function display($template = '', $content = '') {
		$this -> view -> display($template, $content);
	}

	/**
	 * 输出内容
	 * @param $content 内容
	 */
	public function show($content) {
		$this -> view -> display(null, $content);
	}

	/**
	 * 操作成功提示
	 * @param string $msg 提示信息
	 * @param string $tips 小提示
	 * @param string $redirect 跳转地址
	 * @param int $delay 跳转延迟
	 */
	public function success($msg = '操作成功', $tips = '', $redirect = '', $delay = 5) {
		$this -> msg($msg, $tips, $redirect, $delay);
	}

	/**
	 * 操作失败提示
	 * @param string $msg 提示信息
	 * @param string $tips 小提示
	 * @param string $redirect 跳转地址
	 * @param int $delay 跳转延迟
	 */
	public function error($msg = '操作失败', $tips = '', $redirect = '', $delay = 5) {
		$this -> msg($msg, $tips, $redirect, $delay, 1);
	}

	/**
	 * AJAX返回
	 * @param $status 状态码
	 * @param $msg 提示信息
	 * @param array $data AJAX返回数据
	 */
	public function ajax($status, $msg, $data = array()) {
		exit(json_encode(array(
				'status' => $status,
				'msg' => $msg,
				'data' => $data
		)));
	}

	/**
	 * 提示信息方法，为内部调用
	 * @param string $msg 提示信息
	 * @param string $tips 小提示
	 * @param string $redirect 跳转地址
	 * @param int $delay 跳转延迟
	 * @param int $status 状态码
	 */
	protected function msg($msg, $tips = '', $redirect = '', $delay = 5, $status = 0) {
		if(IS_AJAX){
			$this -> ajax($status, $msg, array(
					'delay' => $delay,
					'redirect' => $redirect,
					'tips' => $tips
			));
		}else{
			$t = array(
					'msg' => $msg,
					'tip' => $tips,
					'redirect' => empty($redirect) ? (empty($_SERVER['HTTP_REFERER']) ? 'http://' . __DOMAIN__ : $_SERVER['HTTP_REFERER']) : $redirect,
					'delay' => $delay,
					'status' => $status
			);
			include (LITE_PATH . 'Template/app_msg.php');
			exit();
		}
	}


	/**
	 * ACTION 魔术方法
	 * @param $method 方法名
	 * @param $args 参数
	 */
	public function __call($method, $args) {
		if(0 === strcasecmp($method, __ACTION__)){
			if(method_exists($this, '_empty')) $this -> _empty($method, $args);
			else{
				$path = $this -> view -> parseViewFile($method);
				if($path === false) E(L('ERROR_ACTION') . ' : ' . __ACTION__);
				$this -> view -> display();
			}
		}else
			E(L('METHOD_NOT_EXIST') . ':' . __ACTION__);
	}
}
