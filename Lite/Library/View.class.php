<?php
if (!defined('LITE_PATH')) exit();

class View {
	private $controller;
	public function __construct($controller) {
		$this -> controller = $controller;
	}

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

	public function parseViewFile($template) {
		$path = APP_VIEW . $this -> controller . '/' . $template . '.' . C('VIEW_EXT', null, 'php');
		if (!is_file($path)) return false;
		return $path;
	}

	private function parseView($path) {
		if (!is_file($path)) E(L('TEMPLATE_NOT_EXIST'));
		include $path;
	}
}
