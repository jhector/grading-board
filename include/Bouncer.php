<?php
class Bouncer
{
	protected $db;
	protected $client;
	protected $table;
	protected $master_pass;

	protected $max_tries;

	public function __construct($db, $master_pass)
	{
		$this->db = $db;
		$this->client = "c".substr(md5($_SERVER['REMOTE_ADDR']), 0, 15);
		$this->table = "t".substr(md5($_SERVER['REMOTE_ADDR']), 0, 15);
		$this->master_pass = $master_pass;

		$this->max_tries = 10;
	}

	public function guard()
	{
		if (!$this->clientExists()) {
			if (($ret = $this->clientCreate()) < 0)
				throw new Exception("oh boy, can't create new client: $ret mysql error :(");
			if (($ret = $this->clientNewToken()) < 0)
				throw new Exception("oO can't create new token: $ret mysql error :(");

			return $this->reconnect();
		}

		$tries = $this->clientGetTries();
		if ($tries < 0 || $tries > $this->max_tries) {
			if ($this->clientRefreshToken() < 0)
				throw new Exception("Couldn't refresh token");
		} else {
			$this->clientIncTries();
		}

		return $this->reconnect();
	}

	public function clientGetToken()
	{
		$row = $this->db->select('token', $this->table, 'WHERE id=1');

		return $row[0]['token'];
	}

	private function reconnect()
	{
		mysql_close();

		return new Database($this->db->getHost(),
					$this->client,
					$this->master_pass,
					$this->db->getDB(),
					$this->db->getPrefix());
	}

	private function clientIncTries()
	{
		$qry = "UPDATE {$this->table} SET tries = tries + 1 WHERE id=1";
		if (!$this->db->raw($qry))
			return -1;

		return 0;
	}

	private function clientGetTries()
	{
		$row = $this->db->select('tries', $this->table, 'WHERE id=1');

		return $row[0]['tries'];
	}

	private function clientNewToken()
	{
		$new_token = $this->newToken();
		$qry = "INSERT INTO {$this->table}(token, tries) VALUES ('$new_token', 0)";
		if (!$this->db->raw($qry))
			return -1;

		return 0;
	}

	private function clientRefreshToken()
	{
		$new_token = $this->newToken();
		$qry = "UPDATE {$this->table} SET token='$new_token', tries=0 WHERE id=1";
		if (!$this->db->raw($qry))
			return -1;

		return 0;
	}

	private function clientExists()
	{
		$qry = "SELECT * FROM client WHERE username='$this->client'";
		$res = $this->db->raw($qry);

		return (mysql_num_rows($res) >= 1);
	}

	private function clientCreate()
	{
		$db_name = $this->db->getDB();
		$pass = $this->master_pass;
		$client = $this->client;
		$table = $this->table;

		$qry = "CREATE USER '$client'@'localhost' IDENTIFIED BY '$pass'";
		if (!$this->db->raw($qry))
			return -1;

		$qry = "REVOKE ALL PRIVILEGES, GRANT OPTION FROM '$client'@'localhost'";
		if (!$this->db->raw($qry))
			return -2;

		$qry = "CREATE TABLE `$table` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `token` VARCHAR(40), `tries` INT)";
		if (!$this->db->raw($qry))
			return -3;

		$qry = "GRANT SELECT ON $db_name.$table TO '$client'@'localhost'";
		if (!$this->db->raw($qry))
			return -4;

		$qry = "GRANT SELECT ON $db_name.student TO '$client'@'localhost'";
		if (!$this->db->raw($qry))
			return -5;

		$qry = "INSERT INTO client(username, tablename) VALUES('$client', '$table')";
		if (!$this->db->raw($qry))
			return -6;

		return 0;
	}

	private function newToken()
	{
		$fp = fopen('/dev/urandom','rb');
		$string = fread($fp, 16);
		fclose($fp);

		return sha1($string);
	}
}
?>
