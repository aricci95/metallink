<?php

class Annonce extends AppModel
{

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
                annonce.annonce_id,
                annonce_title,
                annonce_content,
                photo_url,
                annonce.user_id,
                user_photo_url
            FROM
                annonce
            LEFT JOIN photo ON (annonce.annonce_id = photo.key_id)
            JOIN user ON (annonce.user_id = user.user_id)
            LEFT JOIN city ON (user.ville_id = city.ville_id)
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
