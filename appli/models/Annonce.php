<?php

class Annonce extends AppModel
{

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

    public function getSearch($criterias, $offset = 0)
    {
        $contextUserId = $this->context->get('user_id');

        $where = '';

        if (!empty($criterias['search_keyword'])) {
            $where .= " AND annonce_title REGEXP :search_keyword ";
        }

        if (!empty($criterias['search_distance'])) {
            $longitude = $this->context->get('ville_longitude_deg');
            $latitude = $this->context->get('ville_latitude_deg');

            $where .= ' AND ville_longitude_deg BETWEEN :longitude_begin AND :longitude_end
                        AND ville_latitude_deg BETWEEN :latitude_begin AND :latitude_end ';
        }

        $sql = 'SELECT
                *
            FROM
                annonce
            JOIN user ON (annonce.user_id = user.user_id)
            JOIN city ON (user.ville_id = city.ville_id)
            WHERE TRUE
            ' . $where . '
            ORDER BY annonce_date DESC
            LIMIT :limit_begin, :limit_end;
        ';

        $sql = str_replace(',)', ')', $sql);
        $sql = str_replace(', )', ')', $sql);

        $stmt = $this->db->prepare($sql);

        if (!empty($criterias['search_keyword'])) {
            $keywords = explode(' ', $criterias['search_keyword']);

            $regexp = implode('|', $keywords);

            $stmt->bindValue('search_keyword', $regexp, PDO::PARAM_STR);
        }

        if (!empty($criterias['search_distance'])) {
            $ratio = COEF_DISTANCE * $criterias['search_distance'];

            $stmt->bindValue('longitude_begin', ($longitude - $ratio), PDO::PARAM_INT);
            $stmt->bindValue('longitude_end', ($longitude + $ratio), PDO::PARAM_INT);

            $stmt->bindValue('latitude_begin', ($latitude - $ratio), PDO::PARAM_INT);
            $stmt->bindValue('latitude_end', ($latitude + $ratio), PDO::PARAM_INT);
        }

        $stmt->bindValue('limit_begin', $offset * NB_SEARCH_RESULTS, PDO::PARAM_INT);
        $stmt->bindValue('limit_end', NB_SEARCH_RESULTS, PDO::PARAM_INT);

        return $this->db->executeStmt($stmt)->fetchAll();
    }
}
