<?php
/**
 * Copyright (c) 2010-2014 Zulwi Studio All Rights Reserved.
 * Author  @JerryLocke
 * Date    2014/7/27
 * Blog    http://Jerry.hk
 * Email   i@Jerry.hk
 * Team    http://www.zhuwei.cc
 */

if(! defined('LITE_PATH')) exit;

return array(
	'DB_CONFIG' => array(
		'DB_TYPE' => 'mysql', //数据库类型
		'DB_FILE' =>'db.db', //数据库文件地址，仅在数据库类型为 sqlite 时有效
		'DB_HOST' => 'localhost', //数据库地址
		'DB_PORT' => 3306, //数据库端口
		'DB_USER' => 'sign', //数据库用户名
		'DB_PWD' => 'nzjZj7Bxn5LM9RJa', //数据库密码
		'DB_NAME' => 'sign', //数据库名
		'PCONNECT' => true, //持久连接
		'USE_PDO' => true, //是否强制使用PDO，仅在数据库类型为 MySQL 时生效
		),
	'URL_MODEL' => 1, //URL模式
	);
