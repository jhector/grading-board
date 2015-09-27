<?php
require_once 'vendor/autoload.php';

$controllers = array(
	'Base',
	'Default'
);

$loader = new Twig_Loader_Filesystem('skeleton');
$twig = new Twig_Environment($loader);
?>
