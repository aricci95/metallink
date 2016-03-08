<?php

abstract class AppModel extends Model
{

    public $table;
    public $primary;

    public function getTable()
    {
         if (empty($this->table)) {
            $this->table = strtolower(get_called_class());
        }

        return $this->table;
    }

    public function getPrimary()
    {
        if (empty($this->primary)) {
            $this->primary = $this->getTable() . '_id';
        }

        return $this->primary;
    }

    public function count(array $where = array(), array $orderBy = array(), $limit = null)
    {
        $attributes_string = 'count(*) AS counter';

        $data = $this->_queryBuilder($this->getTable(), $attributes_string, $where, $orderBy, $limit);

        return (int) $data[0]['counter'];
    }

    public function find(array $attributes = array(), array $where = array(), array $orderBy = array(), $limit = null)
    {
        $attributes_string = empty($attributes) ? '*' : implode(',', $attributes);

        return $this->_queryBuilder($this->getTable(), $attributes_string, $where, $orderBy, $limit);
    }

    public function findById($id, array $attributes = array(), array $orderBy = array(), $limit = null)
    {
        $attributes_string = empty($attributes) ? '*' : implode(',', $attributes);

        $where = array(
            $this->getPrimary() => (int) $id,
        );

        $results = $this->_queryBuilder($this->getTable(), $attributes_string, $where, $orderBy, $limit);

        return empty($results[0]) ? array() : $results[0];
    }

    public function updateById($id, $attributes, $newValue = null)
    {
        if (is_array($attributes)) {
            $sql = 'UPDATE ' . $this->getTable() . ' SET ';

            foreach ($attributes as $key => $value) {
                $sql .= $key . ' = ' . ':' . $key . ', ';
            }

            $sql .= 'WHERE ' . $this->getPrimary() . ' = :id;';

            $sql = str_replace(', WHERE', ' WHERE', $sql);

            $stmt = $this->db->prepare($sql);

            foreach ($attributes as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
        } else {
            $sql = 'UPDATE ' . $this->getTable() . ' SET ' . $attributes . ' = :new_value WHERE ' . $this->getPrimary() . ' = :id;';

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(':new_value', $newValue);
        }

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $this->db->executeStmt($stmt);
    }

    public function deleteById($id)
    {
        $sql = 'DELETE FROM ' . $this->getTable() . ' WHERE ' . $this->getPrimary() . ' = :id';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('id', (int) $id, PDO::PARAM_INT);

        return $this->db->executeStmt($stmt);
    }

    public function insert(array $values)
    {
        $sql = 'INSERT INTO ' . $this->getTable() . ' (' . implode(', ', array_keys($values)) . ')  VALUES (';

        $valuesToBind = array();
        foreach ($values as $key => $value) {
            $valuesToBind[] = ':' . $key;
        }

        $sql .= implode(', ', $valuesToBind) .' );';

        $stmt = $this->db->prepare($sql);

        foreach ($values as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $this->db->executeStmt($stmt);

        return $this->db->lastInsertId();
    }

}
