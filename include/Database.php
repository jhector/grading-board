<?php
class Database
{
	protected $prefix;
	protected $db_name;
	protected $host;

	public function __construct($host, $user, $pass, $db, $prefix)
	{
		$this->prefix = $prefix;
		$this->db_name = $db;
		$this->host = $host;

		if (!mysql_connect($host, $user, $pass))
			throw new Exception('Couldn\'t connect to database, some mysql error :(');

		if (!mysql_select_db($db))
			throw new Exception('Database doesn\'t exist some mysql error :( ');
	}

	public function select($what, $table, $condition)
	{
		$qry = "SELECT $what FROM {$this->prefix}{$table} $condition";
		$result = mysql_query($qry);

		if (!$result)
			throw new Exception("oO something is wrong with the select statement");

		$ret = array();
		while ($row = mysql_fetch_assoc($result)) {
			array_push($ret, $row);
		}

		return $ret;
	}

	public function raw($query)
	{
		return mysql_query($query);
	}

	public function getDB()
	{
		return $this->db_name;
	}

	public function getHost()
	{
		return $this->host;
	}

	public function getPrefix()
	{
		return $this->prefix;
	}
}
?>
