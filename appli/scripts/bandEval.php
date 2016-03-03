#!/usr/local/bin/php
<?php
session_name("metallink");
session_start();

include_once('../engine/cron/cronBootStrap.php');

require ROOT_DIR . '/appli/crons/BandEvalCron.php';

$cron = new BandEvalCron();
$cron->execute();
?>
