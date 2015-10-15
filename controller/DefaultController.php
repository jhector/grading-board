<?php
class DefaultController extends BaseController
{
	public function indexAction($db)
	{
		$this->render($this->site.'.twig', $this->vars);
	}

	public function searchAction($db)
	{
		$cond = "WHERE first_name='{$_REQUEST['name']}'
			 OR last_name='{$_REQUEST['name']}'
			 ORDER BY id ASC LIMIT 5";

		$data = $db->select('id, first_name, last_name', 'student', $cond);

		$this->vars['students'] = $data;
		$this->render($this->site.'.twig', $this->vars);
	}
}
?>
