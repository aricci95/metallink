<?php

class City extends AppModel
{

    public function suggest($string)
    {
        $sql = "SELECT ville_nom_reel, ville_id, LEFT(ville_code_postal, 2) as ville_code_postal FROM city
                WHERE ville_nom_reel LIKE :string
                ORDER BY ville_population_2010 DESC, ville_nom_reel
                LIMIT 0, 5";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('string', $string . '%', PDO::PARAM_STR);

        return $this->db->executeStmt($stmt)->fetchAll();
    }
}
