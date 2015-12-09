<?php
function ErrorHandler($errno, $errstr, $errfile, $errline)
{
    // Bypass, cas particulier erreur serialize
    if ($errno == 8 && strpos($errstr, 'nserialize()')) {
        return true;
    }
    $message = "Message d'erreur :<br/><b>$errstr</b><br/><br/>".
               "Dans le fichier : <br/><b>$errfile</b><br/><br/>".
               "A la ligne : <b>$errline</b>";
    $exception = new Exception($message, $errno);
    
    require_once ROOT_DIR.'/appli/models/Mailer.php';
    $mailer = new Mailer();
    $mailer->sendError($exception);
    /* Ne pas exécuter le gestionnaire interne de PHP */
    return true;
}
