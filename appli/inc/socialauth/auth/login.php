<?php session_start();
/*==========================================*\
|| ##########################################||
|| # SONHLAB.com - SONHlab Social Auth v2 #build - 0002
|| ##########################################||
\*==========================================*/

// Set Auth Station URL
$index='../station.php';

// Reset unneccessary session
unset($_SESSION['ssa_return_url']);


// User Logged in
if ( isset($_SESSION["userprofile"]) ) {
	header("Location: ".$_SESSION['authstation']);
}
// Options to Log in
else {

	// Get social connect
	$_SESSION['app'] = $_GET['app'];

	// Prepare App configurations
	require_once('appconf.php');

	if ( !empty($_SESSION['app']) ) {

		if ( $_SESSION['app'] == 'facebook' ) { // Facebook Auth
			$app_path = './platforms/facebook-app/facebook.php';
			header("Location: $app_path");
		}
		elseif ( $_SESSION['app'] == 'twitter' ) { // Twitter Auth
			$app_path = './platforms/twitter-app/twitter.php';
			header("Location: $app_path");
		}
		elseif ( $_SESSION['app'] == 'google' ) {  // Google Auth
			$app_path = './platforms/google-app/google.php';
			header("Location: $app_path");
		}
		else {
			// Return Auth Station
			header("Location: ".$_SESSION['authstation']);
		}
	}
	else {
		// Return Auth Station
		header("Location: ".$_SESSION['authstation']);
	}
}
