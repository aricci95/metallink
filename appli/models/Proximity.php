<?php

class Proximity extends AppModel
{

    public function getCloseVilles($latitude, $longitude, $range = 50)
    {
        if ($latitude > 0 && $longitude > 0) {
            $sql = "SELECT code_postal, (6366*acos(cos(radians(".$latitude."))*cos(radians(`latitude`))*cos(radians(`longitude`)
                    -radians(".$longitude."))+sin(radians(".$latitude."))*sin(radians(`latitude`)))) AS distance
                    FROM ville
                    HAVING distance <= ($range / 100)
                    ORDER BY distance;";
            return $this->fetch($sql);
        } else {
            return array();
        }
    }
    /*
	public function getCloseVillesZipCodes($latitude, $longitude, $range = 10) {
        $sql = "SELECT nom, code_postal, (6366*acos(cos(radians(".$latitude."))*cos(radians(`latitude`))*cos(radians(`longitude`)
                -radians(".$longitude."))+sin(radians(".$latitude."))*sin(radians(`latitude`)))) AS distance
                FROM ville
                HAVING distance <= ($range / 100)
                ORDER BY distance;";
        $villes = $this->fetch($sql);

        $villeProches = array();
        foreach($villes as $ville) {
            $villeProches[]['ville_nom_simple']      = $ville['ville_nom_simple'];
            $villeProches[]['distance'] = $ville['distance'] * 100;
        }

        return $villeProches;
    }*/
/*
    public function getVilleCoordinates($villeID) {
        $result = mysql_fetch_object(mysql_query("SELECT longitude, latitude FROM ville WHERE ville_id = '$villeID'"));

        if(isset($result->longitude) && isset($result->latitude)) return $result;
        else return false;
    }

	public function getVilleIDFromCP($cp) {
        $result = $this->fetchOnly("SELECT ville_id FROM ville where code_postal = '".$cp."'");
        if(count($result) == 0) {
            $result = $this->fetchOnly("SELECT ville_id FROM ville where code_postal = '".$cp{0}.$cp{1}."000'");
        }

        if($result['ville_id']) return $result;
        else return false;
    }*/
}
