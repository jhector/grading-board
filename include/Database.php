<?php
class Database
{
	protected $prefix;

	public function __construct($host, $user, $pass, $db, $prefix)
	{
		$this->prefix = $prefix;

		if (!mysql_connect($host, $user, $pass))
			throw new Exception('Couldn\'t connect to database: '.mysql_error());

		if (!mysql_select_db($db))
			throw new Exception('Database doesn\'t exist: '.mysql_error());
	}

	public function select($what, $table, $condition)
	{
		if (preg_match('/[^a-zA-Z0-9_]union[^a-zA-Z0-9_]/i', $condition))
			throw new Exception('oO come on, stop being silly...');

		$qry = "SELECT $what FROM {$this->prefix}{$table} $condition";
		$result = mysql_query($qry);

		if (!$result)
			throw new Exception(mysql_error());

		$ret = array();
		while ($row = mysql_fetch_array($result))
			array_push($ret, $row);

		return $ret;
	}

	public function sanitize($input)
	{
		$blacklist = array('\'', '"', '/', '*');

		return str_replace($blacklist, '', $input);
	}
}
?>
