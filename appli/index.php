<?php
session_name("metallink");
session_start();

// Récupération du chemin absolu
$path = dirname(__FILE__);
$path =  str_replace("\appli", '', $path);
$path =  str_replace("/appli", '', $path);
define('ROOT_DIR', $path);

// inculusion de la conf et des constantes
require ROOT_DIR . '/config/params.php';

// APPLICATION BOOTSTRAP
// CONTROLLER
if(!empty($_GET['page']) && ucfirst($_GET['page']) != 'Home') {
    $page = ucfirst($_GET['page']).'Controller';
    if(!file_exists(ROOT_DIR.'/appli/controllers/'.$page.'.php')) {
        $page = 'HomeController';
    }
} else {
    $page = 'HomeController';
}
// ACTION
$action = 'render';
if (!empty($_GET['action']) && ucfirst($_GET['action']) != 'Home') {
    $action .= ucfirst($_GET['action']);
}

// Loading application files
require ROOT_DIR . '/appli/engine/EngineObject.php';
require ROOT_DIR . '/appli/engine/Log.php';
require ROOT_DIR . '/appli/engine/view/AppView.php';
require ROOT_DIR . '/appli/engine/model/Db.php';
require ROOT_DIR . '/appli/engine/model/Model.php';
require ROOT_DIR . '/appli/engine/model/AppModel.php';
require ROOT_DIR . '/appli/engine/controller/Controller.php';
require ROOT_DIR . '/appli/engine/controller/AppController.php';
require ROOT_DIR . '/appli/models/Tools.php';
require ROOT_DIR . '/appli/models/User.php';
require ROOT_DIR . '/appli/views/ViewHelper.php';
// gestionnaire d'erreurs
include ROOT_DIR.'/appli/engine/ErrorHandler.php';
set_error_handler("ErrorHandler");


try {
    require_once ROOT_DIR . '/appli/controllers/'.$page.'.php';
    $controller = new $page();
    $controller->$action();
} catch (Exception $e) {
    include ROOT_DIR.'/appli/views/maintenance.htm';
    $controller->getModel()->load('mailer')->sendError($e);
    die;
}

?>
