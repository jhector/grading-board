<?php
class BaseController
{
	protected $action;
	protected $vars;
	protected $site;

	public function __construct()
	{
		if (isset($_REQUEST['action']))
			$this->action = strtolower($_REQUEST['action']).'Action';
		else
			$this->action = 'indexAction';

		if (isset($_REQUEST['site']))
			$this->site = strtolower($_REQUEST['site']);
		else
			$this->site = 'default';

		$this->vars = array();
	}

	public function run()
	{
		if (in_array($this->action, get_class_methods($this)))
			$this->{$this->action}();
		else
			throw new Exception("BaseController can't handle action: ".$this->action);
	}
}
?>
