<?php
class BoardController extends BaseController
{
	public function indexAction($db)
	{
		$this->render($this->site.'.twig', array());
	}

	public function searchAction($db)
	{
		$cond = "WHERE first_name='{$db->sanitize($_REQUEST['name'])}'
			 OR last_name='{$db->sanitize($_REQUEST['name'])}'
			 LIMIT 8";

		$data = $db->select('id, first_name, last_name', 'student', $cond);

		$this->render($this->site.'.twig', array('students' => $data));
	}
}
?>
