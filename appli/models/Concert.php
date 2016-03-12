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

    public function add(array $data)
    {
        $clean = array(
            'non précis&eacute;e',
            'non communiqu&eacute;',
            'non pr',
        );

        foreach ($data as $key => $value) {
            if (in_array($value, $clean)) {
                $data[$key] = null;
            }
        }

        $sql = '
            INSERT IGNORE INTO concert (
                external_id,
                organization,
                flyer_url,
                club,
                location,
                price,
                mail_orga,
                fb_event,
                departement,
                date,
                ville_id,
                concert_libel
            ) VALUES (
                :external_id,
                :organization,
                :flyer_url,
                :club,
                :location,
                :price,
                :mail_orga,
                :fb_event,
                :departement,
                :date,
                :ville_id,
                :concert_libel
            );
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('external_id', $data['concert_id']);
        $stmt->bindValue('organization', $data['organization']);
        $stmt->bindValue('flyer_url', $data['flyer']);
        $stmt->bindValue('club', $data['salle']);
        $stmt->bindValue('location', $data['adresse']);
        $stmt->bindValue('price', $data['prix']);
        $stmt->bindValue('mail_orga', $data['contacter']);
        $stmt->bindValue('fb_event', $data['event']);
        $stmt->bindValue('departement', $data['departement']);
        $stmt->bindValue('date', $data['date_timestamp']);
        $stmt->bindValue('ville_id', $data['ville_id']);
        $stmt->bindValue('concert_libel', $this->buildConcertLibel($data));

        if ($this->db->executeStmt($stmt)) {

            $concertId = $this->db->lastInsertId();

            foreach ($data['bands'] as $band) {
                $sql = '
                    INSERT IGNORE INTO concert_band (
                        concert_id,
                        band_id
                    ) VALUES (
                        :concert_id,
                        :band_id
                    );
                ';

                $stmt = $this->db->prepare($sql);

                $stmt->bindValue('concert_id', $concertId, PDO::PARAM_INT);
                $stmt->bindValue('band_id', $band['band_id'], PDO::PARAM_INT);

                $this->db->executeStmt($stmt);
            }
        }
    }

    public function suggestFromUser()
    {
        $concerts = array();

        $sql = '
            SELECT
                *
            FROM
                concert
            LEFT JOIN (
                ville,
                concert_band,
                band
            ) ON (
                concert.ville_id = ville.ville_id
                AND concert.concert_id = concert_band.concert_id
                AND band.band_id = concert_band.band_id
            )
			WHERE concert.ville_id > 0
			AND flyer_url IS NOT NULL
			AND fb_event IS NOT NULL
			AND date > UNIX_TIMESTAMP()
            ORDER BY date ASC
			LIMIT 50;
        ';

        $stmt = $this->db->prepare($sql);

        $concertRows = $this->db->executeStmt($stmt)->fetchAll();

        $tmp_id = 0;
        foreach ($concertRows as $key => $concert) {
            if ($tmp_id != $concert['concert_id']) {
                $bands = array();
            }

            if (!empty($concert['bands'])) {
                $bands = $concerts[$concert['concert_id']]['bands'];
            }

            $concerts[$concert['concert_id']] = $concert;

            $bands[] = array(
                'band_id' => $concert['band_id'],
                'band_libel' => $concert['band_libel'],
                'band_website' => $concert['band_website'],
            );

            $concerts[$concert['concert_id']]['bands'] = $bands;

            $tmp_id = $concert['concert_id'];
        }

        return $concerts;
    }

    public function buildConcertLibel($data)
    {
        $concertLibel = '[' . date("d/m H:i", $data['date_timestamp']) . '] ';

        if (count($data['bands']) > 1) {
            foreach ($data['bands'] as $band) {
                $bandNames[] = strtoupper($band['name']);
            }

            $bandList = strtoupper(implode(' + ', $bandNames));
        } else {
            $bandList = $data['bands'][0]['name'];
        }

        $concertLibel .= $bandList;

        if (!empty($data['salle'])) {
            $concertLibel .= '@ ' . $data['salle'];
        }

        if (!empty($data['ville'])) {
            $concertLibel .= ', ' . ucfirst($data['ville']);
        }

        return $concertLibel;
    }

    // Récupéres les utilisateurs par critéres
    public function getSearch($criterias, $offset = 0, $limit = 0)
    {
        $concerts = array();

        $contextUserId = $this->context->get('user_id');

        $where = '';
        if (!empty($criterias['search_keyword'])) {
            $where = " AND concert_libel LIKE :search_keyword ";
        }

        if (!empty($criterias['search_distance'])) {
            $longitude = $this->context->get('user_longitude');
            $latitude = $this->context->get('user_latitude');

            if (!is_array($longitude) && !is_array($latitude)) {
                if ($longitude > 0 && $latitude > 0) {
                    $where .= ' AND ville_longitude_deg BETWEEN :longitude_begin AND :longitude_end
                                AND ville_latitude_deg BETWEEN :latitude_begin AND :latitude_end
                                AND concert.ville_id > 0 ';
                }
            }
        }

        $sql = 'SELECT
                *
            FROM
                concert
            JOIN (
                city,
                concert_band,
                band
            ) ON (
                concert.ville_id = city.ville_id
                AND concert.concert_id = concert_band.concert_id
                AND band.band_id = concert_band.band_id
            )
            WHERE concert.ville_id > 0 ' . $where . '
            AND flyer_url IS NOT NULL
            AND fb_event IS NOT NULL
            AND date > UNIX_TIMESTAMP()
            ORDER BY date ASC
            LIMIT :limit_begin, :limit_end;
        ';

        $sql = str_replace(',)', ')', $sql);
        $sql = str_replace(', )', ')', $sql);

        $stmt = $this->db->prepare($sql);

        if (!empty($criterias['search_keyword'])) {
            $stmt->bindValue('search_keyword', '%'. $criterias['search_keyword'] .'%', PDO::PARAM_STR);
        }

        if (!empty($criterias['search_distance'])) {
            $ratio = COEF_DISTANCE * $criterias['search_distance'];

            $stmt->bindValue('longitude_begin', ($longitude - $ratio), PDO::PARAM_INT);
            $stmt->bindValue('longitude_end', ($longitude + $ratio), PDO::PARAM_INT);

            $stmt->bindValue('latitude_begin', ($latitude - $ratio), PDO::PARAM_INT);
            $stmt->bindValue('latitude_end', ($latitude + $ratio), PDO::PARAM_INT);
        }

        $stmt->bindValue('limit_begin', $offset * (NB_SEARCH_RESULTS * 3), PDO::PARAM_INT);
        $stmt->bindValue('limit_end', empty($limit) ? (NB_SEARCH_RESULTS * 3) : $limit, PDO::PARAM_INT);

        $concertRows = $this->db->executeStmt($stmt)->fetchAll();

        $tmp_id = 0;
        foreach ($concertRows as $key => $concert) {
            if ($tmp_id != $concert['concert_id']) {
                $bands = array();
            }

            if (!empty($concert['bands'])) {
                $bands = $concerts[$concert['concert_id']]['bands'];
            }

            $concerts[$concert['concert_id']] = $concert;

            $bands[] = array(
                'band_id' => $concert['band_id'],
                'band_libel' => $concert['band_libel'],
                'band_website' => $concert['band_website'],
                'band_style' => $concert['band_style'],
            );

            $concerts[$concert['concert_id']]['bands'] = $bands;

            $tmp_id = $concert['concert_id'];
        }

        return $concerts;
    }
}
