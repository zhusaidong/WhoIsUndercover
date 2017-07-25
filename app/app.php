<?php
/**
* app
* @author Zsdroid [635925926@qq.com]
*/
require_once('config.php');

require_once($storageEngine.'.class.php');
$kv = new $storageEngine($config[$storageEngine]);

if(!isset($_GET['act']) or empty($_GET['act']))
{
	exit;
}
$action = $_GET['act'];

require_once('WhoIsUndercover.class.php');
$w      = new WhoIsUndercover($kv,$config[$storageEngine]['keyPrefix'],$_GET);
if(!method_exists($w,$action))
{
	exit;
}
$w->$action();
$kv->close();
