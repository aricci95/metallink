<?php session_start();
/*==========================================*\
|| ##########################################||
|| # SONHLAB.com - SONH Social Auth v2 #
|| # Facebook SDK 4.x #
|| ##########################################||
\*==========================================*/
// Load neccessary files
require_once('autoload.php');


// use these
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

// init app with app id (APPID) and secret (SECRET)
FacebookSession::setDefaultApplication($_SESSION['fb_appid'], $_SESSION['fb_appsecret']);


// login helper with redirect_uri
$_SESSION['ssa_return_url'] = (isset($_SESSION['ssa_return_url']) ? $_SESSION['ssa_return_url'] : null);
if ( !isset($_SESSION['ssa_return_url'])) {
	$_SESSION['ssa_return_url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}
$helper = new FacebookRedirectLoginHelper($_SESSION['ssa_return_url']);



if ( isset($_SESSION['app']) ) {
	if ( $_SESSION['app'] == 'facebook' ) {
		try {
			$session = $helper->getSessionFromRedirect();
		} catch( FacebookRequestException $ex ) {
			// When Facebook returns an error
		} catch( Exception $ex ) {
			// When validation fails or other local issues
		}
	}
	else {
		header("Location: ".$_SESSION['authstation']);
	}
}
else {
	header("Location: ".$_SESSION['authstation']);
}



// see if we have a session
if ( isset( $session ) ) {

	try {

		// graph api request for user data
		$request = new FacebookRequest($session,'GET','/me?fields=id,name,first_name,last_name,email,gender,hometown,age_range,relationship_status,context,verified,cover,events,friendlists,friends,music,photos,picture');
		$response = $request->execute();

		// get response
		$user = $response->getGraphObject()->asArray();

		// Store User data
		$_SESSION["userprofile"] = $user;

		// Remove unnecessary sessions
		unset($_SESSION['ssa_return_url']);
		unset($_SESSION['fb_appid']);
		unset($_SESSION['fb_appsecret']);

		// Return Auth Station Page
		header("Location: ".$_SESSION['authstation']);

	} catch(FacebookRequestException $e) {

		echo "Exception occured, code: " . $e->getCode();
		echo " with message: " . $e->getMessage();

	}

} else {
	// Access Facebook to auth
	$loginUrl = $helper->getLoginUrl( array( 'email', 'user_friends' ));
	header("Location: $loginUrl");
}
