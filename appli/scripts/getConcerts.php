#!/usr/local/bin/php
<?php
session_name("metallink");
session_start();

include_once('../engine/cron/cronBootStrap.php');

require ROOT_DIR . '/appli/crons/ConcertsCron.php';

$cron = new ConcertsCron();
$cron->execute();
?>
