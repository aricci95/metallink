<?php
// Récupération du chemin absolu
$removedStrings = array(
    'crons',
    'appli',
    '\\\\',
);

$path =  str_replace($removedStrings, array(), dirname(__FILE__));

define('ROOT_DIR', $path);

// inculusion de la conf et des constantes
require ROOT_DIR . '/config/params.php';

// AutoLoad function
function autoLoader($class_name) {
    require ROOT_DIR . '/appli/models/' . $class_name . '.php';
}

spl_autoload_register('autoLoader');


// Loading application files
require ROOT_DIR . '/appli/engine/Context.php';
require ROOT_DIR . '/appli/engine/Log.php';
require ROOT_DIR . '/appli/engine/view/AppView.php';

// Models
require ROOT_DIR . '/appli/engine/model/Db.php';
require ROOT_DIR . '/appli/engine/model/Model.php';
require ROOT_DIR . '/appli/engine/model/AppModel.php';
require ROOT_DIR . '/appli/engine/model/Manager.php';

// Services
require ROOT_DIR . '/appli/engine/service/Service.php';
require ROOT_DIR . '/appli/engine/service/Container.php';

// Controllers
require ROOT_DIR . '/appli/engine/controller/Controller.php';
require ROOT_DIR . '/appli/engine/controller/AppController.php';

// Classes propres au site
require ROOT_DIR . '/appli/models/User.php';
require ROOT_DIR . '/appli/models/Link.php';
require ROOT_DIR . '/appli/views/ViewHelper.php';

// gestionnaire d'erreurs
include ROOT_DIR . '/appli/engine/ErrorHandler.php';
set_error_handler("ErrorHandler");

require_once ROOT_DIR . '/appli/controllers/CronController.php';

