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
}
?>
