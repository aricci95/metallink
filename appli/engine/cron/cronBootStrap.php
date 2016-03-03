<?php
// Récupération du chemin absolu
$removedStrings = array(
    'engine',
    'cron',
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

// Models
require ROOT_DIR . '/appli/engine/cron/Cron.php';
require ROOT_DIR . '/appli/engine/model/Db.php';
require ROOT_DIR . '/appli/engine/model/Model.php';
require ROOT_DIR . '/appli/engine/model/AppModel.php';
require ROOT_DIR . '/appli/engine/model/Manager.php';

// Services
require ROOT_DIR . '/appli/engine/service/Service.php';
require ROOT_DIR . '/appli/engine/service/Container.php';

// Classes propres au site
require ROOT_DIR . '/appli/models/User.php';
require ROOT_DIR . '/appli/models/Link.php';

// gestionnaire d'erreurs
include ROOT_DIR . '/appli/engine/ErrorHandler.php';
set_error_handler("ErrorHandler");