<?php
class DefaultController extends BaseController
{
	public function indexAction()
	{
		$this->render($this->site.'.twig', $this->vars);
	}
}
?>
