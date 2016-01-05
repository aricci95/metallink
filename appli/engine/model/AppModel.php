<?php

abstract class AppModel extends Model
{

    public static function getTable()
    {
        return strtolower(get_called_class());
    }

    public static function getPrimary()
    {
        return self::getTable() . '_id';
    }

    public static function count(array $where = array(), array $orderBy = array(), $limit = null)
    {
        $attributes_string = 'count(*) AS counter';

        $data = self::_queryBuilder(self::getTable(), $attributes_string, $where, $orderBy, $limit);

        return (int) $data[0]['counter'];
    }

    public static function find(array $attributes = array(), array $where = array(), array $orderBy = array(), $limit = null)
    {
        $attributes_string = empty($attributes) ? '*' : implode(',', $attributes);

        return self::_queryBuilder(self::getTable(), $attributes_string, $where, $orderBy, $limit);
    }

    public static function findById($id, array $attributes = array(), array $orderBy = array(), $limit = null)
    {
        $attributes_string = empty($attributes) ? '*' : implode(',', $attributes);

        $where = array(
            self::getPrimary() => (int) $id,
        );

        $results = self::_queryBuilder(self::getTable(), $attributes_string, $where, $orderBy, $limit);

        return empty($results[0]) ? array() : $results[0];
    }

    public static function deleteById($id)
    {
        $sql = 'DELETE FROM ' . self::getTable() . ' WHERE ' . self::getPrimary() . ' = :id';

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue('id', (int) $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function insert(array $values)
    {
        $sql = 'INSERT INTO ' . self::getTable() . ' (' . implode(', ', array_keys($values)) . ')  VALUES (';

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

        if(!$stmt->execute()) {
            $error_message = $stmt->errorInfo();
            throw new Exception('La requête suivante : <b><br/>' . $sql . '</b><br/><br/>a renvoyé une erreur :<br/><i>' . $error_message[2] . '<i>', ERROR_SQL);
        }

        return Db::getInstance()->lastInsertId();
    }

}
