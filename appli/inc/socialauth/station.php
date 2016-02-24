<?php
session_name("metallink");
session_start();

$_SESSION['authstation'] = (isset($_SESSION['authstation']) ? $_SESSION['authstation'] : null);

if (!isset($_SESSION['authstation'])) {
  $_SESSION['authstation'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

if (!isset($_SESSION["userprofile"])) {
  header('location:auth/login.php?app=facebook');
} else {
  header('location:../../../../');
}

exit;
