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

/**
 * 数据库适配器抽象类
 * Class DBAdapter
 */
abstract class DBAdapter {
	/**
	 * @var 连接资源
	 */
	protected $linkId;
	/**
	 * @var 查询资源
	 */
	protected $queryId;
	/**
	 * @var int 查询结果行数
	 */
	protected $numRows = 0;
	/**
	 * @var 最后一次查询的SQL语句
	 */
	protected $lastSql = '';
	/**
	 * @var string 错误信息
	 */
	protected $errorInfo = '';
	/**
	 * @var bool 是否已连接
	 */
	protected $connected = false;

	/**
	 * 连接数据库
	 * @param $config 配置
	 * @return mixed 连接实例
	 */
	public abstract function connect($config);

	/**
	 * 生成SQL语句
	 * @param $clause 条件
	 * @return mixed 生成的SQL语句
	 */
	public abstract function buildSql($clause);

	/**
	 * 释放结果集
	 * @return mixed 结果
	 */
	public abstract function free();

	/**
	 * 关闭数据库
	 * @return mixed 结果
	 */
	public abstract function close();

	/**
	 * 取得上一次错误信息
	 * @return mixed 错误信息
	 */
	public abstract function error();

	/**
	 * 连接SQL语句
	 * @param $clause 条件
	 * @return string 连接后的SQL语句
	 */
	protected function implode($clause) {
		if (is_array($clause)) $clause = implode(',', $clause);
		return $clause;
	}

	public function getLastSql(){
		return $this->lastSql;
	}
}
