<?php
if (!defined('LITE_PATH')) exit();

abstract class Controller {
	private $view;
	public function __construct() {
		$this -> view = new View(__MODULE__);
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
				if ($path === false) E(L('ERROR_ACTION') . ':' . __ACTION__);
				$this -> view -> display();
			}
		} else E(L('METHOD_NOT_EXIST') . ':' . __ACTION__);
	}
}
