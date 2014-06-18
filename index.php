<?php
define('APP_PATH', './App/');
define('APP_DEBUG', true);
require './Lite/Lite.php';
echo START_TIME . '<br />';
// throw new Exception('测试错误');
C('test', 555);
echo C('test', null, '55555');
