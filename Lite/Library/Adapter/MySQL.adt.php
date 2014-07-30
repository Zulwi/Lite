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
		$dbVersion = mysql_get_server_info($this ->linkId);
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

	public function exec($sql, $getAffectedRows = false) {
		$this->lastSql = $sql;
		$flag = mysql_query($sql);
		return $getAffectedRows ? ($flag ? mysql_affected_rows($this->linkId) : false) : $flag;
	}

	private function getResult() {
		$result = array();
		if ($this->numRows>0) {
			while ($row = mysql_fetch_assoc($this ->queryId)) {
				$result[] = $row;
			}
			mysql_data_seek($this->queryId, 0);
		}
		return $result;
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

	public function getLastInsertId() {
		return mysql_insert_id($this ->linkId);
	}

	public function close() {
		if ($this ->linkId) {
			$this->free();
			mysql_close($this ->linkId);
		}
		$this ->linkId = null;
	}
}
