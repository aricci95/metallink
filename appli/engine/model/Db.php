<?php

class Db
{
    private static $_PDO = null;

    public static function getInstance()
    {
        if (empty(self::$_PDO)) {
            self::$_PDO = new PDO('mysql:host=' . _HOST . ';dbname=' . _BASE_DE_DONNEES . ';charset=utf8', _USER, _PASS);

            if (mysqli_connect_error()) {
                echo "<meta http-equiv='REFRESH' content='0;URL=../views/maintenance.htm'>";
                printf("Echec de la connexion : %s\n", mysqli_connect_error());
                exit();
            }
        }

        return self::$_PDO;
    }

    /**
    * Assure la déconnection à la base de données
    */
    public static function close()
    {
        self::getInstance()->close();
    }

    public static function executeStmt(PDOStatement $stmt)
    {
        if (PROFILER) {
            $begin_time = microtime(true);
        }

        $response = $stmt->execute();

        if (PROFILER) {
            $end_time =  microtime(true);
            $executionTime = $end_time - $begin_time;

            if ($executionTime >= 0.0025) {
                echo '<div class="debug"><br/>' . $sql . '<br/><br/>' . $executionTime . '<br/></div>';
            }
        }

        if (!$response) {
            $error_message = $stmt->errorInfo();

            throw new Exception('La requete suivante :<b><br/>' . $stmt->queryString . '</b><br/>a renvoye une erreur:<br/><i>' . $error_message[2] . '</i>', ERROR_SQL);
        };

        return $stmt;
    }

}
