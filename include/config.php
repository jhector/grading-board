<?php
require_once 'vendor/autoload.php';

$config['db_host'] = 'localhost';
$config['db_user'] = 'root'; // TODO: change
$config['db_pass'] = 'root'; // TODO: change
$config['db_name'] = 'course_cs';
$config['db_pref'] = '';

$config['master_pass'] = '!yyi{KVg)IB9vA}^2Q3=||MHD$S5<eDn';

define("FLAG", 'flag{St4yiNg_UnD3R_Th3_R4Dar_I$_s0_1337}');

$controllers = array(
	'Base',
	'Default',
	'Panel'
);

$loader = new Twig_Loader_Filesystem('skeleton');
$twig = new Twig_Environment($loader);
?>
