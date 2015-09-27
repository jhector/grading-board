<?php
require_once 'include/config.php';

foreach ($controllers as $controller) {
	include 'controller/'.$controller.'Controller.php';
}

try {
	$front = new DefaultController();

	if (isset($_REQUEST['site']))
		$controller = ucfirst($strtolower($_REQUEST['site'])).'Controller';
	else
		$controller = '';

	if (class_exists($controller))
		$front = new $controller();

	$front->run();
} catch (Exception $e) {
	$template = $twig->loadTemplate('error.twig');
	echo $template->render(array(
		'error' => htmlspecialchars($e->getMessage())
	));
}
?>
