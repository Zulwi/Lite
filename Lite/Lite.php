<?php
/**
 * Copyright (c) 2010-2014 Zulwi Studio All Rights Reserved.
 * Author  @JerryLocke
 * Date    2014/7/27
 * Blog    http://Jerry.hk
 * Email   i@Jerry.hk
 * Team    http://www.zhuwei.cc
 */

define('START_TIME', microtime(true)); //记录系统开始时间
define('TIMESTAMP', $_SERVER['REQUEST_TIME']); //当前时间戳

define('LITE_VERSION', '1.0.0 α'); // Lite 版本号
defined('LITE_PATH') or define('LITE_PATH', dirname(__FILE__) . '/'); // Lite框架根目录
define('COMMON_PATH', LITE_PATH . 'Common/'); // Lite 系统配置文件目录
define('LIB_PATH', LITE_PATH . 'Library/'); // Lite 系统类库目录
define('LANG_PATH', LITE_PATH . 'Language/'); // Lite 系统语言文件目录

defined('APP_DEBUG') or define('APP_DEBUG', false); // APP调试
defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . '/App/'); // 项目根目录
defined('APP_LIB') or define('APP_LIB', APP_PATH . 'Library/'); // 项目类库目录
defined('COMMON_GROUP') or define('COMMON_GROUP', APP_PATH . 'Common/'); // 项目公共分组
defined('CONTROLLER_DIR') or define('CONTROLLER_DIR', 'Controller/'); // 项目控制器类目录名
defined('MODEL_DIR') or define('MODEL_DIR', 'Model/'); // 项目模型类目录名
defined('VIEW_DIR') or define('VIEW_DIR', 'View/'); // 项目视图类目录名
defined('COMMON_DIR') or define('COMMON_DIR', 'Common/'); // 项目配置文件目录名
defined('LANG_DIR') or define('LANG_DIR', 'Language/'); // 项目语言文件目录名
defined('CONFIG_DIR') or define('CONFIG_DIR', 'Common/'); // 配置文件目录名
defined('CONFIG_FILE') or define('CONFIG_FILE', 'config.php'); // 配置文件名
defined('FUNCTION_FILE') or define('FUNCTION_FILE', 'function.php'); // 函数库文件名

if (!defined('__ROOT__')) { // 网站根目录
	$root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
	define('__ROOT__', (($root=='/' || $root=='\\') ? '' : $root));
}
defined('__PUBLIC__') or define('__PUBLIC__', __ROOT__ . 'Public/');// 网站公共资源目录
defined('__STATIC__') or define('__STATIC__', __PUBLIC__ . 'static/');// 静态资源目录
defined('__UPLOAD__') or define('__UPLOAD__', __PUBLIC__ . 'upload/');// 上传文件目录
defined('__CSS__') or define('__CSS__', __STATIC__ . 'css/');// CSS文件目录
defined('__IMG__') or define('__IMG__', __STATIC__ . 'img/');// 图片文件目录
defined('__JS__') or define('__JS__', __STATIC__ . 'js/');// JavaScript文件目录

require LIB_PATH . 'Lite.class.php';
Lite :: start();
