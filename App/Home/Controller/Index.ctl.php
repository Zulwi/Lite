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
		dump($db->table('sign_log')->field(array('SUM(exp)' => 'exp'))->group('uid')->select());
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
		echo '6.在 sign_log 表查询 uid 值为 4 且 date 值为 20140517 的签到记录<br />';
		dump($db->table(array('sign_log' => 'l'))->join(array('_table' => 'my_tieba', '_as' => 't', '_on' => 't.tid = l.tid', '_type' => 'LEFT JOIN'))->where(array('l.uid' => '4', 'l.date' => '20140517'))->order('l.uid DESC')->select());
		echo '生成的SQL：' . $db->getLastSql() . '<br />影响条数：' . intval($flag) . '<br /><br />';
		echo '用时' . (microtime(true)-START_TIME)*1000 . '毫秒<br /><br />';
		echo '【GET测试】<br />GET值：';
		dump($_GET);
	}
}
