<?php
if (!defined('LITE_PATH')) exit();

class View {
	private $controllerName;
	public function __construct($controller) {
		$this -> controllerName = $controller;
	}

	public function display($template = '', $content = '') {
		if (empty($template)) {
			if (empty($content)) {
				$this -> parseView($this -> parseViewFile(__ACTION__));
			} else {
				ob_clean();
				echo $content;
			}
		} else {
			$this -> parseView($this -> parseViewFile($template));
		}
	}

	public function parseViewFile($template) {
		$path = APP_VIEW . $this -> controllerName . $template;
		if (!is_file($path)) return false;
		return $path
	}

	private function parseView($path) {
		if (!is_file($path)) E('模板文件缺失：' . $path);
		include $path;
	}
}
