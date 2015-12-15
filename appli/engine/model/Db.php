<?php
class Db
{
    var $_mysqli = null;

  /**
   * Initialisation de la classe générale d'accès aux données
   */
    public function Db()
    {
        $this->connect();
    }


    public function connect()
    {
        $mysqli = new mysqli(_HOST, _USER, _PASS, _BASE_DE_DONNEES);
        $mysqli->set_charset("utf8");
      /* Vérification de la connexion */
        if (mysqli_connect_error()) {
            echo "<meta http-equiv='REFRESH' content='0;URL=../views/maintenance.htm'>";
            printf("Echec de la connexion : %s\n", mysqli_connect_error());
            exit();
        }
        $this->_mysqli = $mysqli;
    }

    public function securize($val)
    {
        if (is_numeric($val)) {
            return $val;
        } else {
            $val = $this->_mysqli->real_escape_string(htmlentities($val, ENT_QUOTES));
        }
        return $val;
    }


  /**
   * Assure la déconnection à la base de données
   */
    public function close()
    {
        $this->_mysqli->close();
    }


  /**
   * Exécute les requêtes sql qui lui son injectées
   *
   * @return  boolean   retourne TRUE en cas de succès ou FALSE en cas d'erreur
   */
    public function execute($sql)
    {
        if (PROFILER) {
            $begin_time = microtime(true);
        }

        $response = $this->_mysqli->query($sql);

        if (PROFILER) {
            $end_time =  microtime(true);
            $executionTime = $end_time - $begin_time;
            if ($executionTime >= 0.0025) {
                echo '<div class="debug"><br/>'.$sql.'<br/><br/>'.$executionTime.'<br/></div>';
            }
        }

        if (!$response) {
            throw new Exception('La requête suivante : <b><br/>'.$sql.'</b><br/><br/>a renvoyé une erreur :<br/><i>'.mysqli_error($this->_mysqli).'<i>', ERROR_SQL);
        };

        return $response;
    }

  /**
   * Exécute une requête et renvoie un tableau en récupérant toutes les lignes de données à extraire
   *
   * @param  string  $sql       chaîne contenant la requête SQL à exécuter
   * @param  boolean $echo      true | false; si true-> affiche la requête SQL lors de son exécution
   * @return array              tableau en récupérant toutes les lignes de données à extraire
   */
    public function fetch($sql)
    {

        $result = $this->execute($sql);
        $all = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $all[] = $row;
        }
        return $all;
    }

/**
   * Exécute une requête et renvoie un tableau avec une seule entrée
   *
   * @param  string  $sql       chaîne contenant la requête SQL à exécuter
   * @param  boolean $echo      true | false; si true-> affiche la requête SQL lors de son exécution
   * @return array              tableau en récupérant toutes les lignes de données à extraire
   */
    public function fetchOnly($sql)
    {
        $result = $this->fetch($sql);
        if (!empty($result[0])) {
            return $result[0];
        } else {
            return array();
        }
    }


  /**
   * connaitre l'id (auto_incrément) créé par le dernier INSERT
   *
   * @return int = l'id créé
   */
    public function insertId()
    {
        return $this->_mysqli->insert_id;
    }

  /**
   * Remise en forme format MySQL d'une date d'un autre format
   *
   * exemple : $db->outputDateMysqlFormat($date, 'AAAA-MM-JJ', $return=null)
   * @param  $date   string  date à transformer, format chaîne
   * @param  $format string  format de la date d'entrée, exemple 'AAAA-MM-JJ'
   * @param  $return string  null | chaîne à renvoyer si échec mise en forme date
   * @return boolean
   */
    public function outputDateMysqlFormat($date, $format, $return = null)
    {
        if (strlen(trim($date))!=0) {
            switch ($format) {
                case 'AAAA-MM-JJ':
                    list($aa, $mm, $jj) = explode('-', $date);
                    return $jj.'/'.$mm.'/'.$aa;
                case 'JJ/MM/AAAA':
                    list($jj, $mm, $aa) = explode('/', $date);
                    return $aa.'-'.$mm.'-'.$jj;
                case 'JJ/MM/AAAA HH:MM':
                    list($subStr1, $subStr2) = explode(' ', $date);
                    if ($subStr2=='24:00') {
                        $subStr2='23:59';
                    }
                    list($jj, $mm, $aa) = explode('/', $subStr1);
                    return $aa.'-'.$mm.'-'.$jj.' '.$subStr2.':00';
                case 'JJ/MM/AAAA HH:MM:SS':
                    list($subStr1, $subStr2) = explode(' ', $date);
                    if ($subStr2=='24:00:00') {
                        $subStr2='23:59:59';
                    }
                    list($jj, $mm, $aa) = explode('/', $subStr1);
                    return $aa.'-'.$mm.'-'.$jj.' '.$subStr2;
            }
        } elseif (strlen(trim($date))==0) {
            return $return;
        }
        return $return;
    }
}
