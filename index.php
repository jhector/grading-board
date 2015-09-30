<?php
error_reporting(E_ALL & ~E_DEPRECATED);

require_once 'include/config.php';

include 'include/Database.php';
include 'include/Bouncer.php';

foreach ($controllers as $controller) {
	include 'controller/'.$controller.'Controller.php';
}

try {
	$db = new Database(
		$config['db_host'],
		$config['db_user'],
		$config['db_pass'],
		$config['db_name'],
		$config['db_pref']
	);

	$bouncer = new Bouncer($db, $config['master_pass']);

	$db = $bouncer->guard();

	$front = new DefaultController();

	if (isset($_REQUEST['site']))
		$controller = ucfirst(strtolower($_REQUEST['site'])).'Controller';
	else
		$controller = '';

	if (class_exists($controller))
		$front = new $controller();

	$front->run($db);
} catch (Exception $e) {
	$template = $twig->loadTemplate('error.twig');
	echo $template->render(array(
		'error' => htmlspecialchars($e->getMessage())
	));
}
?>
