<?php
if (!defined('LITE_PATH')) exit();

class Db {
	private static $_instance = array();
	private $adapter;
	private $config;
	private $lastSql;
	private $clause;

	public static function getInstance($config = '') {
		$guid = toGUIDString($config);
		if (!isset(self :: $_instance[$guid])) {
			$obj = new Db();
			self :: $_instance[$guid] = $obj -> factory($config);
		}
		return self :: $_instance[$guid];
	}

	public function factory($config = '') {
		$this -> config = $this -> parseConfig($config);
		switch ($this -> config['type']) {
			case 'mysql':
				$type = 'MySQL';
				break;
			case 'mssql':
				$type = 'MsSQL';
				break;
			case 'mariadb':
				$type = 'MariaDB';
				break;
			case 'sqlite':
				$type = 'SQLite';
				break;
			default:
				E('不支持的数据库类型：' . $this -> config['type']);
		}
		$classname = $type . 'Adapter';
		$this -> adapter = new $classname($this -> config);
		$this -> resetClause();
		return $this;
	}

	private function parseConfig($config = '') {
		if (is_array($config)) {
			$config = array_change_key_case($config);
			$config = array('type' => strtolower($config['db_type']),
				'username' => $config['db_user'],
				'password' => $config['db_pwd'],
				'host' => $config['db_host'],
				'port' => $config['db_port'],
				'database' => $config['db_name'],
				'charset' => isset($config['db_charset']) ? $config['db_charset'] : 'utf8',
				);
		} else {
			$config = array ('type' => strtolower(C('DB_TYPE', null, 'mysql')),
				'username' => C('DB_USER'),
				'password' => C('DB_PWD'),
				'host' => C('DB_HOST'),
				'port' => C('DB_PORT'),
				'database' => C('DB_NAME'),
				'charset' => C('DB_CHARSET', null, 'utf8'),
				);
		}
		return $config;
	}

	public function query($sql) {
		$this -> lastSql = $sql;
		return $this -> adapter -> query($sql);
	}

	public function exec($sql) {
		$this -> lastSql = $sql;
		return $this -> adapter -> exec($sql);
	}

	public function where($where) {
		if (empty($where)) E(L('PARAM_ERROR') . ':where');
		if (is_array($where)) $this -> clause['where'] = array_unique($where);
		elseif (is_string($where)) $this -> clause['where'][] = $where;
		return $this;
	}

	public function field($field) {
		if (empty($field)) E(L('PARAM_ERROR') . ':field');
		if (is_array($field)) $this -> clause['field'] = array_unique($field);
		elseif (is_string($field)) $this -> clause['field'] = explode(',', $field);
		return $this;
	}

	public function table($table) {
		if (empty($table)) E(L('PARAM_ERROR') . ':table');
		if (is_array($table)) $this -> clause['table'] = array_unique($table);
		elseif (is_string($table)) $this -> clause['table'] = explode(',', $table);
		return $this;
	}

	public function limit($num1, $num2 = 0) {
		$this -> clause['limit'][0] = $num1;
		if (isset($this -> clause['limit'][1])) unset($this -> clause['limit'][1]);
		if ($num2 != 0) $this -> clause['limit'][1] = $num2;
		return $this;
	}

	public function find() {
		$this -> limit(1);
		$this -> clause['type'] = 'find';
		return $this -> query($this -> buildSql());
	}

	public function select($num = 0) {
		if ($num != 0) $this -> limit($num);
		$this -> clause['type'] = 'select';
		return $this -> query($this -> buildSql());
	}

	public function insert($insert) {
		if (empty($insert)) E(L('PARAM_ERROR') . ':insert');
		if (is_array($insert)) $this -> clause['insert'] = array_unique($insert);
		elseif (is_string($insert)) $this -> clause['insert'] = explode(',', $insert);
		$this -> clause['type'] = 'insert';
		return $this -> query($this -> buildSql());
	}

	public function update($update) {
		if (empty($update)) E(L('PARAM_ERROR') . ':update');
		if (is_array($update)) $this -> clause['update'] = array_unique($update);
		elseif (is_string($update)) $this -> clause['update'] = explode(',', $update);
		$this -> clause['type'] = 'update';
		return $this -> query($this -> buildSql());
	}

	public function delete() {
		if (empty($this -> clause['where'])) E(L('PARAM_ERROR') . ':where');
		$this -> clause['type'] = 'delete';
		return $this -> query($this -> buildSql());
	}

	private function buildSql() {
		$this -> lastSql = $this -> adapter -> buildSql($this -> clause);
		$this -> resetClause();
		return $this -> lastSql;
	}

	private function resetClause() {
		$this -> clause = array();
		$this -> clause['field'] = array();
		$this -> clause['table'] = array();
		$this -> clause['join'] = array();
		$this -> clause['where'] = array();
		$this -> clause['limit'] = array();
		$this -> clause['extra'] = array();
		$this -> clause['type'] = '';
	}

	public function __destruct() {
		$this -> adapter -> close();
	}
}
