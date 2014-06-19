<?php
define('START_TIME', microtime(true));
define('TIMESTAMP', $_SERVER['REQUEST_TIME']);

defined('LITE_PATH') or define('LITE_PATH', __DIR__ . '/');
defined('APP_DEBUG') or define('APP_DEBUG', false);
defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . '/App');
defined('APP_LIB') or define('APP_LIB', APP_PATH . 'Library/');
defined('APP_CONTROLLER') or define('APP_CONTROLLER', APP_PATH . 'Controller/');
defined('APP_MODEL') or define('APP_MODEL', APP_PATH . 'Model/');
defined('APP_VIEW') or define('APP_VIEW', APP_PATH . 'View/');
defined('APP_COMMON') or define('APP_COMMON', APP_PATH . 'Common/');
defined('APP_LANG') or define('APP_LANG', APP_PATH . 'Language/');

define('COMMON_PATH', realpath(LITE_PATH . 'Common') . '/');
define('LIB_PATH', realpath(LITE_PATH . 'Library') . '/');
define('LANG_PATH', realpath(LITE_PATH . 'Language') . '/');
define('LITE_VERSION', '1.0.0 α');
if (!defined('__ROOT__')) {
	$root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
	define('__ROOT__', (($root == '/' || $root == '\\') ? '' : $root));
}
defined('__PUBLIC__') or define('__PUBLIC__', __ROOT__ . 'Public/');
require LIB_PATH . 'Lite.class.php';
Lite :: start();
