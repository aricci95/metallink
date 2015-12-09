#!/usr/local/bin/php
<?php
$path = dirname(__FILE__);
$path =  str_replace("\appli\scripts", '', $path);
$path =  str_replace("/appli/scripts", '', $path);
define('ROOT_DIR', $path);

require_once ROOT_DIR.'/config/params.php';
require_once ROOT_DIR.'/appli/engine/EngineObject.php';
require_once ROOT_DIR.'/appli/engine/controller/AppController.php';
require_once ROOT_DIR.'/appli/controllers/CronController.php';

$controller = new CronController();
$controller->renderUserStatuses();
?>
