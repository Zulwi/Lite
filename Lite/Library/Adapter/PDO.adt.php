<?php
/**
 * Copyright (c) 2010-2014 Zulwi Studio All Rights Reserved.
 * Author  @JerryLocke
 * Date    2014/7/30
 * Blog    http://Jerry.hk
 * Email   i@Jerry.hk
 * Team    http://www.zhuwei.cc
 */

if (!defined('LITE_PATH')) exit;

class PDOAdapter extends DBAdapter {
	public function __construct($config) {
		if (!extension_loaded('pdo')) E(L('DB_UNSUPPORTED') . " : {$config['type']}");
		$this ->connect($config);
	}

	public function connect($config) {
		if (empty($config)) E(L('DB_CONFIG_ERROR'));
		$dsn = $config['type'] . ':';
		$dsn .= !empty($config['host']) ? 'host=' . $config['host'] . ';' : '';
		$dsn .= !empty($config['port']) ? 'port=' . $config['port'] . ';' : '';
		$dsn .= !empty($config['database']) ? 'dbname=' . $config['database'] . ';' : '';
		$dsn .= (!empty($config['file']) && strtolower($config['type'])=='sqlite') ? $config['file'] : '';
		$this ->linkId = new PDO($dsn, $config['username'], $config['password'], array(PDO::ATTR_PERSISTENT => $config['pconnect']));
		if (!$this ->linkId) E(L('DB_CONNECT_ERROR'));
		$this ->connected = true;
	}

	public function query($sql) {
		$this->lastSql = $sql;
		$this->queryId = $this ->linkId->query($sql);
		$result = array();
		if ($this->queryId) $result = $this->queryId->fetchAll();
		$this->numRows = count($result);
		return $this->queryId===false ? false : $result;
	}

	public function exec($sql, $getAffectedRows = false) {
		$this->lastSql = $sql;
		return $this ->linkId->exec($sql);
	}

	public function free() {
		$this->queryId = null;
	}

	public function error() {
		$this->errorInfo = $this ->linkId->errorCode() . ':' . $this ->linkId->errorInfo();
		if (!empty($this->lastSql)) {
			$this->errorInfo .= "\n [SQL] : " . $this->lastSql;
		}
		return $this->errorInfo;
	}

	public function close() {
		$this->free();
		$this ->linkId = null;
	}

	public function getLastInsertId() {
		return $this ->linkId->lastInsertId();
	}
}
