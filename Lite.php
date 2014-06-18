<?php
define('START_TIME', microtime(true));
define('TIMESTAMP', substr(START_TIME, 0, strpos(START_TIME, '.')));
defined('LITE_PATH') or define('LITE_PATH', __DIR__ . '/');
defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . '/App');
defined('APP_DEBUG') or define('APP_DEBUG', false);
define('COMMON_PATH', realpath(LITE_PATH . 'Common') . '/');
define('LIB_PATH', realpath(LITE_PATH . 'Library') . '/');
define('LITE_VERSION', '1.0.0 α');
defined('CONTROLLER_PATH') or define('CONTROLLER_PATH', APP_PATH . 'Controller/');
defined('MODEL_PATH') or define('MODEL_PATH', APP_PATH . 'Model/');
if (!defined('ROOT')) {
	$root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
	define('ROOT', (($root == '/' || $root == '\\')?'':$root));
}
require LIB_PATH . 'Lite.class.php';
Lite :: start();
