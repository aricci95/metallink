<?php

/*
 *  Classe d'accès aux données des villes
 */
class Concert extends AppModel
{

    public function suggest($string)
    {
        $sql = "SELECT distinct concert_libel as libel, concert_id as id FROM concert
                WHERE concert_libel LIKE '$string%'
                ORDER BY concert_libel
                LIMIT 0, 10";
        return $this->fetch($sql);
    }

    public function add($string)
    {
        $this->execute("INSERT INTO concert (concert_libel) VALUES ('".$string."')");
        return $this->db->lastInsertId();
    }
}
