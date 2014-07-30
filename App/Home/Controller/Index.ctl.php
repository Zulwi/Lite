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
		echo '【DB类测试】<br />';
		$db = DB::getInstance();
		echo '1.查询 Cache 表 k 值为 plugins 的记录（单条）<br />记录信息：';
		dump($db->table('cache')->field(array('k', 'v'))->where(array('k' => 'plugins'))->find());
		echo '生成的SQL：' . $db->getLastSql() . '<br /><br />';
		echo '2.查询 sign_log 表的全部记录并统计签到经验（多条）<br />记录信息：';
		dump($db->table('sign_log')->field(array('SUM(exp)'=>'exp'))->group('uid')->select());
		echo '生成的SQL：' . $db->getLastSql() . '<br /><br />';
		echo '3.在 Cache 表插入一条记录<br />';
		$data = array('k' => 'test', 'v' => 'test');
		$flag = $db->table('cache')->insert($data);
		echo '生成的SQL：' . $db->getLastSql() . '<br />影响条数：' . intval($flag) . '<br /><br />';
		echo '4.在 Cache 表更新 k 值为 plugins 的记录<br />';
		$data['v'] = "testnow";
		$flag = $db->table('cache')->where(array('k' => 'test'))->update($data);
		echo '生成的SQL：' . $db->getLastSql() . '<br />影响条数：' . intval($flag) . '<br /><br />';
		echo '5.在 Cache 表删除 k 值为 plugins 的记录<br />';
		$flag = $db->table('cache')->where(array('k' => 'test'))->delete();
		echo '生成的SQL：' . $db->getLastSql() . '<br />影响条数：' . intval($flag) . '<br /><br />';
		echo '用时' . (microtime(true)-START_TIME)*1000 . '毫秒';
	}
}
