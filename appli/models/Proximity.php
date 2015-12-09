<?php

class Proximity extends AppModel
{

    public function getCloseVilles($lattitude, $longitude, $range = 50)
    {
        if ($lattitude > 0 && $longitude > 0) {
            $sql = "SELECT code_postal, (6366*acos(cos(radians(".$lattitude."))*cos(radians(`lattitude`))*cos(radians(`longitude`) 
                    -radians(".$longitude."))+sin(radians(".$lattitude."))*sin(radians(`lattitude`)))) AS distance 
                    FROM ville
                    HAVING distance <= ($range / 100)
                    ORDER BY distance;";
            return $this->fetch($sql);
        } else {
            return array();
        }
    }
    /*
	public function getCloseVillesZipCodes($lattitude, $longitude, $range = 10) {
        $sql = "SELECT nom, code_postal, (6366*acos(cos(radians(".$lattitude."))*cos(radians(`lattitude`))*cos(radians(`longitude`) 
                -radians(".$longitude."))+sin(radians(".$lattitude."))*sin(radians(`lattitude`)))) AS distance 
                FROM ville
                HAVING distance <= ($range / 100)
                ORDER BY distance;";
        $villes = $this->fetch($sql);
        
        $villeProches = array();
        foreach($villes as $ville) {
            $villeProches[]['nom']      = $ville['nom'];
            $villeProches[]['distance'] = $ville['distance'] * 100;
        }
        
        return $villeProches;
    }*/
/*
    public function getVilleCoordinates($villeID) {
        $result = mysql_fetch_object(mysql_query("SELECT longitude, lattitude FROM ville WHERE ville_id = '$villeID'"));

        if(isset($result->longitude) && isset($result->lattitude)) return $result;
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
