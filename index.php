<?php
define('APP_PATH', './App/');
define('APP_DEBUG', true);
require './Lite/Lite.php';
echo START_TIME . '<br />';
// throw new Exception('测试错误');
$DB = DB :: getInstance();
$DB->table('member')->select();
dump($DB);