#!/usr/local/bin/php
<?php
session_name("metallink");
session_start();

include_once('cronBootStrap.php');

$controller = new CronController();
$controller->renderGetConcerts();
?>
