<?php

abstract class AppModel extends Model
{

    public function getTable()
    {
        return strtolower(get_called_class());
    }

    public function getPrimary()
    {
        return $this->getTable() . '_id';
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

    public function updateById($id, $attribute, $newValue)
    {
        $sql = 'UPDATE ' . $this->getTable() . ' SET ' . $attribute . ' = :new_value WHERE ' . $this->getPrimary() . ' = :id;';

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue(':new_value', $newValue);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return Db::executeStmt($stmt);
    }

    public function deleteById($id)
    {
        $sql = 'DELETE FROM ' . $this->getTable() . ' WHERE ' . $this->getPrimary() . ' = :id';

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue('id', (int) $id, PDO::PARAM_INT);

        return Db::executeStmt($stmt);
    }

    public function insert(array $values)
    {
        $sql = 'INSERT INTO ' . $this->getTable() . ' (' . implode(', ', array_keys($values)) . ')  VALUES (';

        $valuesToBind = array();
        foreach ($values as $key => $value) {
            $valuesToBind[] = ':' . $key;
        }

        $sql .= implode(', ', $valuesToBind) .' );';

        $stmt = Db::getInstance()->prepare($sql);

        foreach ($values as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        Db::executeStmt($stmt);

        return Db::getInstance()->lastInsertId();
    }

}
