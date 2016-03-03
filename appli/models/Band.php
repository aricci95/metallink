<?php

class Band extends AppModel
{

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
            INSERT INTO band (
                band_libel,
                band_website,
                band_style
            ) VALUES (
                :band_libel,
                :band_website,
                :band_style
            );
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('band_libel', $data['name']);
        $stmt->bindValue('band_website', $data['website']);
        $stmt->bindValue('band_style', $data['style']);

        if ($this->db->executeStmt($stmt)) {
            return $this->db->lastInsertId();
        }
    }

    public function update(array $data)
    {
        $sql ='
            UPDATE band SET';

        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $sql .= ', ' . $key . ' = ' . ':' . $key;
            }
        }

        $sql = str_replace('SET,', 'SET', $sql);

        $sql .= ' WHERE band_libel LIKE :band_libel_like ;';

        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->bindValue('band_libel_like', '%' . trim(strtolower($data['band_libel'])) . '%');

        return $this->db->executeStmt($stmt);
    }

    public function findBandsToUpdate($limit)
    {
        $sql = '
            SELECT
                *
            FROM
                band
            WHERE 
                band_logo_url IS NULL
            LIMIT :limit_begin, :limit_end
            ;
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('limit_begin', 0, PDO::PARAM_INT);
        $stmt->bindValue('limit_end', $limit, PDO::PARAM_INT);

        return $this->db->executeStmt($stmt)->fetchAll();
    }
}
