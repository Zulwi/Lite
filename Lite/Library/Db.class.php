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
 * 数据库类
 * Class Db
 */
final class Db {
	/**
	 * @var array 实例集
	 */
	private static $_instance = array();
	/**
	 * @var 数据库类型
	 */
	private $type;
	/**
	 * @var 适配器
	 */
	private $adapter;
	/**
	 * @var 配置
	 */
	private $config;
	/**
	 * @var 查询条件
	 */
	private $clause = array();

	/**
	 * 取得数据库连接实例
	 * @param string $config 配置
	 * @return mixed 实例
	 */
	public static function getInstance($config = '') {
		$guid = toGUIDString($config);
		if (!isset(self :: $_instance[$guid])) {
			$obj = new Db();
			self :: $_instance[$guid] = $obj ->factory($config);
		}
		return self :: $_instance[$guid];
	}

	/**
	 * 工厂方法，根据传入配置来返回对应数据库类型实例
	 * @param string $config 配置
	 * @return $this 实例
	 */
	public function factory($config = '') {
		$this ->config = $this ->parseConfig($config);
		if (strtolower($this ->config['type'])=='mysql' && !$this->config['pdo']) {
			$type = $this->type = 'MySQL';
		} else {
			$type = 'PDO';
			$this->type = $this ->config['type'];
		}
		$classname = $type . 'Adapter';
		$this ->adapter = new $classname($this ->config);
		$this ->resetClause();
		return $this;
	}

	/**
	 * 解析数据库配置
	 * @param string $config 配置
	 * @return array|string 解析后的配置
	 */
	private function parseConfig($config = '') {
		if (is_array($config)) {
			$config = array_change_key_case($config);
			$config = array('type' => strtolower($config['db_type']), 'username' => $config['db_user'], 'password' => $config['db_pwd'], 'host' => $config['db_host'], 'port' => $config['db_port'], 'database' => $config['db_name'], 'charset' => isset($config['db_charset']) ? $config['db_charset'] : 'utf8',);
		} else {
			$config = array_merge(array('DB_TYPE' => 'MySQL', 'DB_HOST' => 'localhost', 'DB_PORT' => 3306, 'DB_USER' => 'root', 'DB_PWD' => '', 'DB_NAME' => 'database', 'DB_CHARSET' => 'utf8', 'DB_FILE' => '', 'PCONNECT' => false, 'USE_PDO' => false), C('DB_CONFIG'));
			if (strtolower($config['DB_TYPE'])=='sqlite') $config['DB_HOST'] = $config['DB_PORT'] = $config['DB_USER'] = $config['DB_PWD'] = $config['DB_NAME'] = '';;
			$config = array('type' => strtolower($config['DB_TYPE']), 'username' => $config['DB_USER'], 'password' => $config['DB_PWD'], 'host' => $config['DB_HOST'], 'port' => $config['DB_PORT'], 'database' => $config['DB_NAME'], 'charset' => $config['DB_CHARSET'], 'file' => $config['DB_FILE'], 'pconnect' => $config['PCONNECT'], 'pdo' => $config['USE_PDO']);
		}
		return $config;
	}

	/**
	 * 进行一次查询
	 * @param $sql SQL语句
	 * @return mixed 查询结果
	 */
	public function query($sql) {
		return $this ->adapter ->query($sql);
	}

	/**
	 * 执行一条SQL语句
	 * @param $sql SQL语句
	 * @param $getAffectedRows 是否返回受影响函数
	 * @return mixed 执行结果
	 */
	public function exec($sql, $getAffectedRows) {
		return $this ->adapter ->exec($sql, $getAffectedRows);
	}

	/**
	 * 添加 where 子句
	 * @param $where 条件
	 * @param string $separator 连接符
	 * @return $this
	 */
	public function where($where, $separator = ' AND ') {
		if (empty($where)) E(L('PARAM_ERROR') . ' : where');
		if (is_array($where)) $this ->clause['where'] = array_unique($where); else $this ->clause['where'][] = $where;
		$this->clause['extra']['separator'] = $separator;
		return $this;
	}

	/**
	 * 指定查询的字段
	 * @param $field 字段
	 * @return $this 实例本身
	 */
	public function field($field) {
		if (empty($field)) E(L('PARAM_ERROR') . ' : field');
		$this ->clause['field'] = is_array($field) ? array_unique($field) : explode(',', $field);
		return $this;
	}

	/**
	 * 指定查询的表
	 * @param $table 表名
	 * @return $this 实例本身
	 */
	public function table($table) {
		if (empty($table)) E(L('PARAM_ERROR') . ' : table');
		$this ->clause['table'] = is_array($table) ? array_unique($table) : explode(',', $table);
		return $this;
	}


	/**
	 * 添加 group by 子句
	 * @param $group
	 * @return $this
	 */
	public function group($group) {
		if (is_string($group)) {
			array_unshift($this->clause['field'], $group);
			$this->clause['group'][] = $group;
		} elseif (is_array($group)) {
			$this->clause['field'] = array_merge($group, $this->clause['field']);
			$this->clause['group'] = array_merge($group, $this->clause['group']);
		}
		return $this;
	}

	/**
	 * 添加 join 子句
	 * @param $join
	 * @return $this
	 */
	public function join($join) {
		$join = func_get_args();
		$table = array();
		foreach ($join as $clause) {
			if (!empty($clause['_table'])) $table[$clause['_table']] = $currentTable = (!empty($clause['_as']) ? $clause['_as'] : $clause['_table']);
			foreach ($clause as $k => $v) {
				if (!in_array($k, array('_on', '_table', '_as', '_type')) || is_numeric($k)) {
					if (is_numeric($k)) {
						$this->clause['field'][] = "{$currentTable}.{$v}";
					} else {
						$this->clause['field']["{$currentTable}.{$k}"] = $v;
					}
				} else {
					$this->clause['join'][$clause['_table']][$k] = $v;
				}
			}
		}
		return $this;
	}

	/**
	 * 添加 limit 子句
	 * @param $num1 起始行/数量
	 * @param int $num2 数量
	 * @return $this 实例本身
	 */
	public function limit($num1, $num2 = 0) {
		$this ->clause['limit'][0] = $num1;
		if (isset($this ->clause['limit'][1])) unset($this ->clause['limit'][1]);
		if ($num2!=0) $this ->clause['limit'][1] = $num2;
		return $this;
	}

	/**
	 * 执行查询并返回一条结果
	 * @param int $where 如果填入此数则清空where参数并按行数查询
	 * @return mixed 结果
	 */
	public function find($where = 0) {
		$this ->limit(1);
		if ($where) {
			$this ->clause['where'] = array();
			$this ->clause['limit'] = array();
			$this->where('id=' . $where);
		}
		$this ->clause['type'] = 'select';
		$result = $this ->query($this ->buildSql());
		return isset($result[0]) ? $result[0] : null;
	}

	/**
	 * 执行查询并返回结果数组
	 * @param int $num 要返回的条数
	 * @return mixed 结果
	 */
	public function select($num = 0) {
		if ($num!=0) $this ->limit($num);
		$this ->clause['type'] = 'select';
		return $this ->query($this ->buildSql());
	}

	/**
	 * 插入数据
	 * @param array $insert 要插入的数据
	 * @param bool $replace 已有重复时是否替换数据
	 * @param bool $ignore 是否忽略错误
	 * @return mixed 执行结果
	 */
	public function insert($insert = array(), $replace = false, $ignore = false) {
		if (empty($insert) || !is_array($insert)) E(L('PARAM_ERROR') . ' : insert');
		$this ->clause['data'] = $insert;
		$this ->clause['type'] = 'insert';
		$this->clause['extra']['replace'] = $replace;
		$this->clause['extra']['ignore'] = $ignore;
		return $this ->exec($this ->buildSql(), true);
	}

	/**
	 * 更新数据
	 * @param $update 要更新的数据
	 * @return mixed 执行结果
	 */
	public function update($update) {
		if (empty($update) || !is_array($update)) E(L('PARAM_ERROR') . ' : update');
		if (is_array($update)) $this ->clause['data'] = $update;
		$this ->clause['type'] = 'update';
		return $this ->exec($this ->buildSql(), true);
	}

	/**
	 * 删除数据
	 * @return mixed 执行结果
	 */
	public function delete() {
		$this ->clause['type'] = 'delete';
		return $this ->exec($this ->buildSql(), true);
	}

	/**
	 * 生成SQL语句
	 * @return mixed SQL语句
	 */
	private function buildSql() {
		$sql = $this ->adapter ->buildSql($this ->clause);
		$this ->resetClause();
		return $sql;
	}

	/**
	 * 重置查询条件
	 */
	private function resetClause() {
		$this ->clause = array();
		$this ->clause['field'] = array();
		$this ->clause['table'] = array();
		$this ->clause['data'] = array();
		$this ->clause['group'] = array();
		$this ->clause['join'] = array();
		$this ->clause['where'] = array();
		$this ->clause['limit'] = array();
		$this ->clause['extra'] = array();
		$this ->clause['type'] = '';
	}

	/**
	 * 取得最后一次查询的SQL语句
	 * @return 最后一次查询的SQL语句
	 */
	public function getLastSql() {
		return $this ->adapter ->getLastSql();
	}

	/**
	 * 关闭连接
	 */
	public function __destruct() {
		if ($this ->adapter) $this ->adapter ->close();
	}
}
