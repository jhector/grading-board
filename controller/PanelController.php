<?php
class PanelController extends BaseController
{
	public function indexAction($db)
	{
		$this->vars['phase_two'] = false;
		$this->render($this->site.'.twig', $this->vars);
	}

	public function loginAction($db)
	{
		$username = $_REQUEST['username'];
		$password = $_REQUEST['password'];

		// password = trolo!Fool
		if ($username !== "kdavis" || sha1($password) !== "c155164c8b0acab5ff5abba0dd1dd5e282af266a") {
			$this->vars['phase_two'] = false;
			$this->vars['error'] = "Invalid credentials";
			$this->render($this->site.'.twig', $this->vars);
		}

		$this->vars['phase_two'] = true;
		$this->render($this->site.'.twig', $this->vars);
	}
}
?>
