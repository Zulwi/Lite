<?php
/**
 * Copyright (c) 2010-2014 Zulwi Studio All Rights Reserved.
 * Author  @JerryLocke
 * Date    2014/7/27
 * Blog    http://Jerry.hk
 * Email   i@Jerry.hk
 * Team    http://www.zhuwei.cc
 */

if (!defined('LITE_PATH')) exit;

class IndexController extends Controller {
	function Index() {
		$db = DB::getInstance();
		//dump($db->table('cache')->field(array('k', 'v'))->where(3)->find(5));
		//echo $db->getLastSql();
		//dump($db->table('cache')->field(array('k', 'v'))->where("k=plugins")->select());
		//echo $db->getLastSql();
		$data = array('k' => 'test', 'v' => 'test');
		$db->table('cache')->insert($data);
		echo $db->getLastSql();
		$data['v'] = "'test`11";
		$db->table('cache')->where(array('k' => 'test'))->update($data);
		echo '<br />';
		echo $db->getLastSql();
	}
}
