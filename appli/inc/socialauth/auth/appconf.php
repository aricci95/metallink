<?php session_start();
/*==========================================*\
|| ##########################################||
|| # SONHLAB.com - SONHlab Social Auth v2 #
|| ##########################################||
\*==========================================*/


if ( $_SESSION['app'] == 'facebook' ) { // Facebook App

	// App ID
	$_SESSION['fb_appid'] = '737628719706534';
	// App Secret
	$_SESSION['fb_appsecret'] = '7177458050c1a2fe0b9bb738a2fb002d';

}
elseif ( $_SESSION['app'] == 'twitter' ) { // Twitter App

	// Consumer Key
	$_SESSION['tt_key'] = '<replace-your-consumer-key-here>';
	// Consumer Secret
	$_SESSION['tt_secret'] = '<replace-your-consumer-secret-here>';

}
elseif ( $_SESSION['app'] == 'google' ) { // Google App

	// Client ID
	$_SESSION['gg_appid'] = '<replace-your-client-id-here>';
	// Client Secret
	$_SESSION['gg_appsecret'] = '<replace-your-client-secret-here>';

}
