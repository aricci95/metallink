<?php session_start();
/*==========================================*\
|| ##########################################||
|| # SONHLAB.com - SONH Social Auth v2 #
|| ##########################################||
\*==========================================*/


//session_destroy();
unset($_SESSION['userprofile']);
unset($_SESSION['app']);

unset($_SESSION['ssa_return_url']);
unset($_SESSION['access_token']);
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

//unset($_SESSION['status']);

$_SESSION['authstation'] = (isset($_SESSION['authstation']) ? $_SESSION['authstation'] : null);
if ( isset($_SESSION['authstation']) ) {
	header("Location: ".$_SESSION['authstation']);
}
else {
	echo 'Sessions have been removed. Please return the home page.';
}
