<?php
class DefaultController extends BaseController
{
	public function indexAction($db)
	{
		$this->render($this->site.'.twig', $this->vars);
	}

	public function searchAction($db)
	{
		$cond = "WHERE first_name='{$db->sanitize($_REQUEST['name'])}'
			 OR last_name='{$db->sanitize($_REQUEST['name'])}'
			 LIMIT 8";

		$data = $db->select('id, first_name, last_name', 'student', $cond);

		$this->vars['students'] = $data;
		$this->render($this->site.'.twig', $this->vars);
	}
}
?>
