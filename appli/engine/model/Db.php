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

}
