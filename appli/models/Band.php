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
            INSERT INTO ref_band (
                band_libel,
                band_website
            ) VALUES (
                :band_libel,
                :band_website
            );
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('band_libel', $data['name']);
        $stmt->bindValue('band_website', $data['website']);

        if ($this->db->executeStmt($stmt)) {
            return $this->db->lastInsertId();
        }
    }
}
