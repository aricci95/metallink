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
                    REPLACE INTO concert_band (
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
                ref_band
            ) ON (
                concert.ville_id = ville.ville_id
                AND concert.concert_id = concert_band.concert_id
                AND ref_band.band_id = concert_band.band_id
            )
            WHERE concert.ville_id = :ville_id
            ORDER BY date DESC
            LIMIT :limit;
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('ville_id', $this->context->get('ville_id'), PDO::PARAM_INT);
        $stmt->bindValue('limit', 50, PDO::PARAM_INT);

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
            $concertLibel .= ', ' . $data['ville'];
        }

        return $concertLibel;
    }
}
