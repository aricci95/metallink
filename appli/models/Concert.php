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
            REPLACE INTO concert (
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
                ville_id
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
                :ville_id
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

        if ($this->db->executeStmt($stmt)) {

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

                $stmt->bindValue('concert_id', $this->db->lastInsertId(), PDO::PARAM_INT);
                $stmt->bindValue('band_id', $band['band_id'], PDO::PARAM_INT);

                $this->db->executeStmt($stmt);
            }

        }
    }

    public function suggestFromUser($results = 1)
    {
        $sql = '
            SELECT
                *
            FROM
                concert
            JOIN (
                concert_band,
                ref_band
            ) ON (
                concert.concert_id = concert_band.concert_id
                AND concert_band.band_id = ref_band.band_id
            )
            WHERE price > 0
            AND flyer_url IS NOT NULL
            ORDER BY date DESC
            LIMIT :limit;
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('limit', $results, PDO::PARAM_INT);

        return $this->db->executeStmt($stmt)->fetchAll();
    }
}
