<?php
if (!defined('LITE_PATH')) exit();

class Controller {
	private $view;
	public function __construct() {
		$this -> view = new View(__CLASS__);
		if (method_exists($this, '_init')) $this -> _init();
	}

	public function display($template = '', $content = '') {
		$this -> view -> display($template, $content);
	}

	public function show($content) {
		$this -> view -> display(null, $content);
	}

	public function __call($method, $args) {
		if (0 === strcasecmp($method, __ACTION__)) {
			if (method_exists($this, '_empty')) $this -> _empty($method, $args);
			else {
				$path = $this -> view -> parseViewFile($method);
				if ($path === false) E('Action 错误' . ':' . method);
				$this -> view -> display();
			}
		} else E(__CLASS__ . '类不存在 Method：' . $method);
	}
}
