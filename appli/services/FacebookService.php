<?php

require ROOT_DIR . '/appli/inc/socialauth/auth/platforms/facebook-app/Facebook/FacebookSession.php';
require ROOT_DIR . '/appli/inc/socialauth/auth/platforms/facebook-app/Facebook/GraphObject.php';
require ROOT_DIR . '/appli/inc/socialauth/auth/platforms/facebook-app/Facebook/FacebookRedirectLoginHelper.php';
require ROOT_DIR . '/appli/inc/socialauth/auth/platforms/facebook-app/Facebook/FacebookRequest.php';
require ROOT_DIR . '/appli/inc/socialauth/auth/platforms/facebook-app/Facebook/GraphUser.php';
require ROOT_DIR . '/appli/inc/socialauth/auth/platforms/facebook-app/Facebook/FacebookSDKException.php';
require ROOT_DIR . '/appli/inc/socialauth/auth/platforms/facebook-app/Facebook/FacebookRequestException.php';

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

class FacebookService extends Service
{

    public function login()
    {
        /*
        spl_autoload_register(function ($class)
        {
            // project-specific namespace prefix
            $prefix = 'Facebook\\';

            // base directory for the namespace prefix
            $base_dir = defined('FACEBOOK_SDK_V4_SRC_DIR') ? FACEBOOK_SDK_V4_SRC_DIR : __DIR__ . '/Facebook/';

            // does the class use the namespace prefix?
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                // no, move to the next registered autoloader
                return;
            }

            // get the relative class name
            $relative_class = substr($class, $len);

            // replace the namespace prefix with the base directory, replace namespace
            // separators with directory separators in the relative class name, append
            // with .php
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            // if the file exists, require it
            if (file_exists($file)) {
                require $file;
            }
        });*/

        $_SESSION['authstation'] = (isset($_SESSION['authstation']) ? $_SESSION['authstation'] : null);

        // init app with app id (APPID) and secret (SECRET)
        FacebookSession::setDefaultApplication(FACEBOOK_APP_ID, FACEBOOK_APP_SECRET);


        // login helper with redirect_uri
        $_SESSION['ssa_return_url'] = (isset($_SESSION['ssa_return_url']) ? $_SESSION['ssa_return_url'] : null);

        if ( !isset($_SESSION['ssa_return_url'])) {
            $_SESSION['ssa_return_url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }

        // MOCK A RETIRER
        $_SESSION['ssa_return_url'] = 'http://www.metallink.fr/social/login/facebook';



        $helper = new FacebookRedirectLoginHelper($_SESSION['ssa_return_url']);

        try {

            $session = $helper->getSessionFromRedirect();
        } catch( FacebookRequestException $ex ) {
            echo $ex->getMessage();die;
            // When Facebook returns an error
        } catch( Exception $ex ) {
             echo $ex->getMessage();die;
            // When validation fails or other local issues
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
            $loginUrl = $helper->getLoginUrl( array( 'email' ));
            Log::err($loginUrl);
         //   echo '<pre>' . print_r($_SESSION, true) . '</pre>';die;
            header("Location: $loginUrl");
        }
    }

}
