<?php

class GeolocService extends Service
{

    public function localize()
    {
        $ip_user = '31.32.223.74';

        include(ROOT_DIR . '/appli/inc/geoloc/geoipcity.inc');
        include(ROOT_DIR . '/appli/inc/geoloc/geoipregionvars.php');

        $gi = geoip_open(realpath(ROOT_DIR . '/appli/inc/geoloc/GeoLiteCity.dat'), GEOIP_STANDARD);

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
}