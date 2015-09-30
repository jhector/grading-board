<?php
require_once 'vendor/autoload.php';

$config['db_host'] = 'localhost';
$config['db_user'] = 'root'; // TODO: change
$config['db_pass'] = 'root'; // TODO: change
$config['db_name'] = 'course_cs';
$config['db_pref'] = '';

$controllers = array(
	'Base',
	'Default',
	'Panel'
);

$loader = new Twig_Loader_Filesystem('skeleton');
$twig = new Twig_Environment($loader);
?>
