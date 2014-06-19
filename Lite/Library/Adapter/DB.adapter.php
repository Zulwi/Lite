<?php
if (!defined('LITE_PATH')) exit();
abstract class DBAdapter {
	protected $linkId;
	protected $connected = false;

	public abstract function connect($config);
	public abstract function buildSql($clause);

	protected function implode($clause) {
		if (is_array($clause)) $clause = implode(',', $clause);
		return $clause;
	}
}
