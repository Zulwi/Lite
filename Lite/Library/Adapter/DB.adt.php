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
	 * 进行一次SQL查询
	 * @param $sql
	 * @return mixed
	 */
	public abstract function query($sql);

	/**
	 * 执行一条SQL语句
	 * @param $sql
	 * @return mixed
	 */
	public abstract function exec($sql);

	/**
	 * 生成SQL语句
	 * @param $clause 条件
	 * @return mixed 生成的SQL语句
	 */
	public function buildSql($clause) {
		if (empty($clause['table'])) E(L('NEED_PARAM') . ' : table');
		$sqlTemplate = '%SELECT% %FIELD% %FROM% %TABLE%%DATA% %JOIN% %GROUP% %WHERE% %ORDER% %LIMIT%';
		switch ($clause['type']) {
			case 'select':
				$sqlTemplate = str_replace('%SELECT%', 'SELECT', $sqlTemplate);
				break;
			case 'insert':
				$clause['where'] = array();
				$sqlTemplate = str_replace('%SELECT%', $clause['extra']['replace'] ? 'REPLACE INTO' : ($clause['extra']['ignore'] ? 'INSERT IGNORE INTO' : 'INSERT INTO'), $sqlTemplate);
				break;
			case 'update':
				if (empty($clause['where'])) E(L('NEED_PARAM') . ' : where');
				$sqlTemplate = str_replace('%SELECT%', 'UPDATE', $sqlTemplate);
				break;
			case 'delete':
				if (empty($clause['where'])) E(L('NEED_PARAM') . ' : where');
				$sqlTemplate = str_replace('%SELECT%', 'DELETE', $sqlTemplate);
				break;
		}
		if (empty($clause['field']) && $clause['type']=='select') $clause['field'] = '*';
		$sqlTemplate = str_replace('%FIELD%', $clause['type']=='select' ? $this ->implode($clause['field'], 'field') : '', $sqlTemplate);
		$sqlTemplate = str_replace('%FROM%', ($clause['type']=='select' || $clause['type']=='delete') ? 'FROM' : '', $sqlTemplate);
		$sqlTemplate = str_replace('%TABLE%', $this ->implode($clause['table'], 'table'), $sqlTemplate);
		$sqlTemplate = str_replace('%DATA%', !empty($clause['data']) ? $this->implode($clause['data'], $clause['type']) : '', $sqlTemplate);
		$sqlTemplate = str_replace('%GROUP%', !empty($clause['group']) ? $this ->implode($clause['group'], 'group') : '', $sqlTemplate);
		$sqlTemplate = str_replace('%JOIN%', !empty($clause['join']) ? $this ->implode($clause['join'], 'join') : '', $sqlTemplate);
		$sqlTemplate = str_replace('%ORDER%', !empty($clause['order']) ? 'ORDER BY ' . $clause['order'] : '', $sqlTemplate);
		$sqlTemplate = str_replace('%WHERE%', !empty($clause['where']) ? $this ->implode($clause['where'], 'where', $clause['extra']['separator']) : '', $sqlTemplate);
		if (isset($clause['limit'][0])) {
			$limit = 'LIMIT ' . $clause['limit'][0];
			if (isset($clause['limit'][1])) $limit .= ',' . $clause['limit'][1];
		} else $limit = '';
		$sqlTemplate = str_replace('%LIMIT%', $limit, $sqlTemplate);
		return trim($sqlTemplate) . ';';
	}

	/**
	 * 连接SQL语句
	 * @param array $clause 条件
	 * @param string $type 类型
	 * @param string $separator 连接字符串
	 * @return array|string 连接后的SQL语句
	 */
	private function implode($clause, $type = 'table', $separator = ' AND ') {
		$sql = '';
		if (is_string($clause)) {
			if ($type=='where') $sql .= 'WHERE ';
			$sql .= $clause;
		} elseif (is_array($clause)) {
			switch ($type) {
				case 'field':
					foreach ($clause as $k => $v) {
						$sql .= is_numeric($k) ? "{$v}," : "{$k} as `{$v}`,";
					}
					break;
				case 'table':
					foreach ($clause as $k => $v) {
						$sql .= is_numeric($k) ? "{$v}," : "{$k} {$v},";
					}
					break;
				case 'insert':
					$field = array();
					$value = array();
					foreach ($clause as $k => $v) {
						$field[] = "`{$k}`";
						$value[] = "'" . escapeString($v) . "'";
					}
					$sql .= "(" . implode(',', $field) . ") VALUES(" . implode(',', $value) . ")";
					break;
				case 'update':
					$sql = ' SET ';
					foreach ($clause as $k => $v) {
						$sql .= "`{$k}`='" . escapeString($v) . "',";
					}
					break;
				case 'where':
					$sql = 'WHERE ';
					foreach ($clause as $k => $v) {
						if (is_numeric($k)) {
							$condition = explode('=', $v, 2);
							$sql .= count($condition)==2 ? "{$condition[0]}='{$condition[1]}'" : "{$condition[0]}";
						} else {
							$sql .= "{$k}='" . escapeString($v) . "'{$separator}";
						}
					}
					break;
				case 'group':
					$sql = 'GROUP BY ' . implode(',', $clause);
					break;
				case 'join':
					foreach ($clause as $k => $v) {
						$sql .= "{$v['_type']} {$k} {$v['_as']} ON {$v['_on']}";
					}
					break;
			}
		}
		if ($type=='where' && endsWith($sql, $separator)) $sql = substr($sql, 0, 0-strlen($separator));
		if (endsWith($sql, ',')) $sql = rtrim($sql, ',');
		return $sql;
	}

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
	 * 取得上一次插入的ID
	 * @return mixed
	 */
	public abstract function getLastInsertId();

	/**
	 * @return 最后一次查询的SQL语句
	 */
	public function getLastSql() {
		return $this->lastSql;
	}
}
