<?php
if (!defined('LITE_PATH')) exit();
class MySQLAdapter extends DBAdapter {
	public function __construct($config) {
		if (!extension_loaded('mysql')) E('当前主机不支持 MySQL');
		$this -> connect($config);
	}

	public function connect($config) {
		if (empty($config)) E('数据库配置信息出错');
		$host = $config['host'] . ($config['port']?":{$config['port']}":'');
		$this -> linkId = mysql_connect($host, $config['username'], $config['password'], true, 131072);
		if (!$this -> linkId || (!empty($config['database']) && !mysql_select_db($config['database'], $this -> linkId))) E(mysql_error());
		$dbVersion = mysql_get_server_info($this -> linkId[$linkNum]);
		mysql_query("SET NAMES '" . $config['charset'] . "'", $this -> linkId);
		if ($dbVersion > '5.0.1') mysql_query("SET sql_mode=''", $this -> linkId);
		$this -> connected = true;
	}

	public function buildSql($clause) {
		$sqlTemplate = '%SELECT% %FIELD% %FROM% %TABLE% %DATA% %JOIN% %WHERE% %LIMIT%';
		switch ($clause['type']) {
			case 'select':
				$sqlTemplate = str_replace('%SELECT%', 'SELECT', $sqlTemplate);
				if (empty($clause['field'])) $clause['field'] = '*';
				$sqlTemplate = str_replace('%FIELD%', $this -> implode($clause['field']), $sqlTemplate);
				$sqlTemplate = str_replace('%FROM%', 'FROM', $sqlTemplate);
				if (empty($clause['table'])) E('select 前必须指定 table');
				$sqlTemplate = str_replace('%TABLE%', $this -> implode($clause['table']), $sqlTemplate);
				$sqlTemplate = str_replace('%DATA%', '', $sqlTemplate);
				if (!empty($clause['join'])) $sqlTemplate = str_replace('%JOIN%', $this -> implode($clause['join']), $sqlTemplate);
				else $sqlTemplate = str_replace('%JOIN%', '', $sqlTemplate);
				if (!empty($clause['where'])) $sqlTemplate = str_replace('%WHERE%', 'WHERE ' . $this -> implode($clause['where']), $sqlTemplate);
				else $sqlTemplate = str_replace('%WHERE%', '', $sqlTemplate);
				if (isset($clause['limit'][0])) {
					$limit = 'LIMIT ' . $clause['limit'][0];
					if (!isset($clause['limit'][1])) $limit .= ',' . $clause['limit'][1];
				} else $limit = '';
				$sqlTemplate = str_replace('%LIMIT%', $limit, $sqlTemplate);
				echo $sqlTemplate;
				break;
			case 'find':
				$sqlTemplate = str_replace('%SELECT%', 'SELECT', $sqlTemplate);
				break;
			case 'insert':
				$sqlTemplate = str_replace('%SELECT%', 'INSERT INTO', $sqlTemplate);
				break;
			case 'update':
				$sqlTemplate = str_replace('%SELECT%', 'UPDATE', $sqlTemplate);
				break;
			case 'delete':
				$sqlTemplate = str_replace('%SELECT%', 'DELE', $sqlTemplate);
				break;
			default:
		}
	}

	public function query() {
	}
}
