<?php

/*
 *  Classe d'accès aux données des villes
 */
class Ville extends AppModel
{

    public function suggest($string)
    {
        $sql = "SELECT distinct nom as libel, ville_id as id, code_postal as value FROM ville
                WHERE nom LIKE '$string%'
                ORDER BY nom
                LIMIT 0, 10";
        return $this->fetch($sql);
    }
}
