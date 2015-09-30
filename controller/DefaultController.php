<?php
class DefaultController extends BaseController
{
	public function indexAction($db, $bouncer)
	{
		$this->render($this->site.'.twig', $this->vars);
	}

	public function searchAction($db, $bouncer)
	{
		$cond = "WHERE first_name='{$db->sanitize($_REQUEST['name'])}'
			 OR last_name='{$db->sanitize($_REQUEST['name'])}'
			 LIMIT 5";

		$data = $db->select('id, first_name, last_name', 'student', $cond);

		$this->vars['students'] = $data;
		$this->render($this->site.'.twig', $this->vars);
	}
}
?>
