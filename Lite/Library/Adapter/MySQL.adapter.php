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

class MySQLAdapter extends DBAdapter {
	public function __construct($config) {
		if (!extension_loaded('mysql')) E(L('DB_UNSUPPORTED') . ': MySQL');
		$this ->connect($config);
	}

	public function connect($config) {
		if (empty($config)) E(L('DB_CONFIG_ERROR'));
		$host = $config['host'] . ($config['port'] ? ":{$config['port']}" : '');
		$this ->linkId = mysql_connect($host, $config['username'], $config['password'], true, 131072);
		if (!$this ->linkId || (!empty($config['database']) && !mysql_select_db($config['database'], $this ->linkId))) E(mysql_error());
		$dbVersion = mysql_get_server_info($this ->linkId[$linkNum]);
		mysql_query("SET NAMES '" . $config['charset'] . "'", $this ->linkId);
		if ($dbVersion>'5.0.1') mysql_query("SET sql_mode=''", $this ->linkId);
		$this ->connected = true;
	}

	public function query($sql) {
		$this->lastSql = $sql;
		$this->queryId = mysql_query($sql, $this ->linkId);
		if ($this->queryId===false) {
			return false;
		} else {
			$this->numRows = mysql_num_rows($this->queryId);
			return $this->getResult();
		}
	}

	public function exec($sql) {
		$this->lastSql = $sql;
		return mysql_query($sql);
	}

	private function getResult() {
		$result = array();
		if ($this->numRows>0) {
			while ($row = mysql_fetch_assoc($this->queryId)) {
				$result[] = $row;
			}
			mysql_data_seek($this->queryId, 0);
		}
		return $result;
	}

	public function buildSql($clause) {
		if (empty($clause['table'])) E(L('NEED_PARAM') . ' : table');
		$sqlTemplate = '%SELECT% %FIELD% %FROM% %TABLE%%DATA% %JOIN% %ORDER% %WHERE% %LIMIT%';
		switch ($clause['type']) {
			case 'select':
				$sqlTemplate = str_replace('%SELECT%', 'SELECT', $sqlTemplate);
				break;
			case 'find':
				$sqlTemplate = str_replace('%SELECT%', 'SELECT', $sqlTemplate);
				break;
			case 'insert':
				$clause['field'] = array();
				$clause['where'] = array();
				$sqlTemplate = str_replace('%SELECT%', $clause['extra']['replace'] ? 'REPLACE INTO' : ($clause['extra']['ignore'] ? 'INSERT IGNORE INTO' : 'INSERT INTO'), $sqlTemplate);
				break;
			case 'update':
				if (empty($clause['where'])) E(L('NEED_PARAM') . ' : where');
				$sqlTemplate = str_replace('%SELECT%', 'UPDATE', $sqlTemplate);
				break;
			case 'delete':
				$sqlTemplate = str_replace('%SELECT%', 'DELE', $sqlTemplate);
				break;
		}
		if (empty($clause['field']) && $clause['type']=='select') $clause['field'] = '*';
		$sqlTemplate = str_replace('%FIELD%', $clause['type']=='select' ? $this ->implode($clause['field'], 'field') : '', $sqlTemplate);
		$sqlTemplate = str_replace('%FROM%', $clause['type']=='select' ? 'FROM' : '', $sqlTemplate);
		$sqlTemplate = str_replace('%TABLE%', $this ->implode($clause['table'], 'table'), $sqlTemplate);
		$sqlTemplate = str_replace('%DATA%', !empty($clause['data']) ? $this->implode($clause['data'], $clause['type']) : '', $sqlTemplate);
		if (!empty($clause['join'])) $sqlTemplate = str_replace('%JOIN%', $this ->implode($clause['join'], 'join'), $sqlTemplate); else $sqlTemplate = str_replace('%JOIN%', '', $sqlTemplate);
		$sqlTemplate = str_replace('%ORDER%', isset($clause['order']) ? 'ORDER BY ' . $clause['order'] : '', $sqlTemplate);
		$sqlTemplate = str_replace('%WHERE%', !empty($clause['where']) ? $this ->implode($clause['where'], 'where') : '', $sqlTemplate);
		if (isset($clause['limit'][0])) {
			$limit = 'LIMIT ' . $clause['limit'][0];
			if (isset($clause['limit'][1])) $limit .= ',' . $clause['limit'][1];
		} else $limit = '';
		$sqlTemplate = str_replace('%LIMIT%', $limit, $sqlTemplate);
		return $sqlTemplate;
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
				case 'table':
					foreach ($clause as $k => $v) {
						$sql .= is_numeric($k) ? "`{$v}`," : "`{$k}` as `{$v}`";
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
							$sql .= count($condition)==2 ? "`{$condition[0]}`='{$condition[1]}'" : "{$condition[0]}";
						} else {
							$sql .= "`{$k}`='" . escapeString($v) . "'{$separator}";
						}
					}
					break;
				case 'join':
					//TODO JOIN子句连接方法
					break;
			}
		}
		if ($type=='where' && endsWith($sql, $separator)) $sql = substr($sql, 0, 0-strlen($separator));
		if (endsWith($sql, ',')) $sql = rtrim($sql, ',');
		return $sql;
	}

	public function free() {
		mysql_free_result($this->queryId);
		$this->queryId = null;
	}

	public function error() {
		$this->errorInfo = mysql_errno() . ':' . mysql_error($this->linkId);
		if (!empty($this->lastSql)) {
			$this->errorInfo .= "\n [SQL] : " . $this->lastSql;
		}
		return $this->errorInfo;
	}

	public function close() {
		if ($this ->linkId) {
			$this->free();
			mysql_close($this ->linkId);
		}
		$this ->linkId = null;
	}
}
