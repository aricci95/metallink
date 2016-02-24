<?php session_start();
/*==========================================*\
|| ##########################################||
|| # SONHLAB.com - SONH Social Auth v2 #
|| # Google API #
|| ##########################################||
\*==========================================*/
require_once 'Google/Client.php';
require_once 'Google/Service/Oauth2.php';


// login helper with redirect_uri
$_SESSION['ssa_return_url'] = (isset($_SESSION['ssa_return_url']) ? $_SESSION['ssa_return_url'] : null);
if ( !isset($_SESSION['ssa_return_url'])) {
	$_SESSION['ssa_return_url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}


$client = new Google_Client();
$client->setClientId($_SESSION['gg_appid']);
$client->setClientSecret($_SESSION['gg_appsecret']);
$client->setRedirectUri($_SESSION['ssa_return_url']);
$client->setScopes('email');

$oauth2Service = new Google_Service_Oauth2($client);

/************************************************
  If we're logging out we just need to clear our
  local access token in this case
 ************************************************/
if (isset($_REQUEST['logout'])) {
	unset($_SESSION['access_token']);
}

/************************************************
  If we have a code back from the OAuth 2.0 flow,
  we need to exchange that with the authenticate()
  function. We store the resultant access token
  bundle in the session, and redirect to ourself.
 ************************************************/
if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

/************************************************
  If we have an access token, we can make
  requests, else we generate an authentication URL.
 ************************************************/
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
	$client->setAccessToken($_SESSION['access_token']);
} else {
	$authUrl = $client->createAuthUrl();
	// Access Google to auth
	header("Location: $authUrl");
}

/************************************************
  If we're signed in we can go ahead and retrieve
  the ID token, which is part of the bundle of
  data that is exchange in the authenticate step
  - we only need to do a network call if we have
  to retrieve the Google certificate to verify it,
  and that can be cached.
 ************************************************/
if ($client->getAccessToken()) {

	$_SESSION['access_token'] = $client->getAccessToken();
	
	//$token_data = $client->verifyIdToken()->getAttributes();
	$user = $oauth2Service->userinfo->get();
	
	// Store User data
	$_SESSION["userprofile"] = (array) $user;

	// Return Auth Station Page
	header("Location: ".$_SESSION['authstation']);
	
}