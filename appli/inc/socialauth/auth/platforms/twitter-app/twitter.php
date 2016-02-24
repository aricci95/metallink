<?php session_start();
/*==========================================*\
|| ##########################################||
|| # SONHLAB.com - SONH Social Auth v2 #
|| # Twitter OAuth 1.1 #
|| ##########################################||
\*==========================================*/
require_once('twitteroauth/twitteroauth.php');


// Start Config
$_SESSION['ssa_return_url'] = (isset($_SESSION['ssa_return_url']) ? $_SESSION['ssa_return_url'] : null);
if ( !isset($_SESSION['ssa_return_url'])) {
	$_SESSION['ssa_return_url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}
define('OAUTH_CALLBACK', $_SESSION['ssa_return_url']);
// End Config


if ( isset($_SESSION['oauth_token']) && isset($_SESSION['oauth_token_secret']) ) {

	/* If the oauth_token is old redirect to the connect page. */
	if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
		$_SESSION['oauth_status'] = 'oldtoken';
		header('Location: ../../logout.php');
	}
	
	/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
	$connection = new TwitterOAuth($_SESSION['tt_key'], $_SESSION['tt_secret'], $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

	/* Save the access tokens. Normally these would be saved in a database for future use. */
	$_SESSION['access_token'] = $connection->getAccessToken($_REQUEST['oauth_verifier']);

	/* Remove no longer needed request tokens */
	unset($_SESSION['oauth_token']);
	unset($_SESSION['oauth_token_secret']);

	/* If HTTP response is 200 continue otherwise send to connect page to retry */
	if (200 == $connection->http_code) {

		/* Create a TwitterOauth object with consumer/user tokens. */
		$connection = new TwitterOAuth($_SESSION['tt_key'], $_SESSION['tt_secret'], $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret']);

		/* If method is set change API call made. Test is called by default. */
		$user = $connection->get('account/verify_credentials');

		// Store User data
		$_SESSION["userprofile"] = (array) $user;
		
		// Remove unnecessary session
		unset($_SESSION['ssa_return_url']);
		unset($_SESSION['tt_key']);
		unset($_SESSION['tt_secret']);
		
		// Return Auth Station
		header('Location: '.$_SESSION['authstation']);
	} else {
		/* Save HTTP status for error dialog on connnect page.*/
		header('Location: ../../logout.php');
	}

}
else {

	/* Build TwitterOAuth object with client credentials. */
	//$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
	$connection = new TwitterOAuth($_SESSION['tt_key'], $_SESSION['tt_secret']);
	 
	/* Get temporary credentials. */
	$request_token = $connection->getRequestToken(OAUTH_CALLBACK);

	/* Save temporary credentials to session. */
	$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
	 
	/* If last connection failed don't display authorization link. */
	switch ($connection->http_code) {
		case 200:
			/* Build authorize URL and redirect user to Twitter. */
			$url = $connection->getAuthorizeURL($token);
			header('Location: ' . $url); 
		break;
		default:
			/* Show notification if something went wrong. */
			echo 'Could not connect to Twitter. Refresh the page or try again later.';
	}

}