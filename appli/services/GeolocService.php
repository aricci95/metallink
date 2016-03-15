<?php

class GeolocService extends Service
{

    public function localize()
    {
        //$ip_user = '31.32.223.74';
        $ip_user = '172.20.10.4';

        include(ROOT_DIR . '/libraries/geoloc/geoipcity.inc');
        include(ROOT_DIR . '/libraries/geoloc/geoipregionvars.php');

        $gi = geoip_open(realpath(ROOT_DIR . '/libraries/geoloc/GeoLiteCity.dat'), GEOIP_STANDARD);

        $record = geoip_record_by_addr($gi, $ip_user);
       // $record = geoip_record_by_addr($gi,$_SERVER['REMOTE_ADDR']);

        /*
        echo $record->country_name . "\n";
        echo $GEOIP_REGION_NAME[$record->country_code][$record->region] . "\n";
        echo $record->city . "\n";
        echo $record->postal_code . "\n";
        echo $record->latitude . "\n";
        echo $record->longitude . "\n";*/

        geoip_close($gi);

        return $record;
    }

    public function getCloseDepartments($longitude, $latitude, $distanceMax)
    {
        $closeDepartments = array();

        $departments = $this->model->city->find(array(
            'DISTINCT ville_departement',
            'ville_longitude_deg',
            'ville_latitude_deg',
        ));

        foreach ($departments as $department) {
            if ($this->getDistance($department['ville_longitude_deg'], $department['ville_latitude_deg'], $longitude, $latitude) <= $distanceMax) {
                $closeDepartments[] = $department['ville_departement'];
            }
        }

        return $closeDepartments;
    }

    // echo $this->get('geoloc')->getDistance(48.856667, 2.350987, 45.767299, 4.834329);
    public function getDistance($lat1, $lng1, $lat2, $lng2) {
        $earth_radius = 6378137;   // Terre = sphère de 6378km de rayon

        $rlo1 = deg2rad($lng1);
        $rla1 = deg2rad($lat1);
        $rlo2 = deg2rad($lng2);
        $rla2 = deg2rad($lat2);

        $dlo = ($rlo2 - $rlo1) / 2;
        $dla = ($rla2 - $rla1) / 2;

        $a = (sin($dla) * sin($dla)) + cos($rla1) * cos($rla2) * (sin($dlo) * sin($dlo));
        $d = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round(($earth_radius * $d) / 1000, 0);
    }
}