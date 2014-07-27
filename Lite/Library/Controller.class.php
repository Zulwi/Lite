<?php
if(! defined('LITE_PATH')) exit();

abstract class Controller {
	private $view;
	public function __construct() {
		$this -> view = new View(__MODULE__);
		if(method_exists($this, '_init')) $this -> _init();
	}
	
	public function display($template = '', $content = '') {
		$this -> view -> display($template, $content);
	}
	
	public function show($content) {
		$this -> view -> display(null, $content);
	}

	public function success($msg = '操作成功', $tips = '', $redirect = '', $delay = 5) {
		$this -> msg($msg, $tips, $redirect, $delay);
	}
	
	public function error($msg = '操作失败', $tips = '', $redirect = '', $delay = 5) {
		$this -> msg($msg, $tips, $redirect, $delay, 1);
	}
	
	public function ajax($status, $msg, $data = array()) {
		exit(json_encode(array(
				'status' => $status,
				'msg' => $msg,
				'data' => $data
		)));
	}
	
	public function msg($msg, $tips = '', $redirect = '', $delay = 5, $status = 0) {
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
	

	public function __call($method, $args) {
		if(0 === strcasecmp($method, __ACTION__)){
			if(method_exists($this, '_empty')) $this -> _empty($method, $args);
			else{
				$path = $this -> view -> parseViewFile($method);
				if($path === false) E(L('ERROR_ACTION') . ':' . __ACTION__);
				$this -> view -> display();
			}
		}else
			E(L('METHOD_NOT_EXIST') . ':' . __ACTION__);
	}
}
