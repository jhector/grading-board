<?php
class DefaultController extends BaseController
{
	public function indexAction($db)
	{
		$this->render($this->site.'.twig', $this->vars);
	}
}
?>
