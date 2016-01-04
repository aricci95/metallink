<?php

abstract class AppModel extends Model
{

    private static $_table;
    private static $_primary;

    public static function getTable()
    {
        if (empty(self::$_table)) {
            self::$_table = strtolower(get_called_class());
        }

        return self::$_table;
    }

    public static function getPrimary()
    {
        if (empty(self::$_primary)) {
            self::$_primary = self::$_table . '_id';
        }

        return self::$_primary;
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

        return $results[0];
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

        $sql .=  implode(', ', $valuesToBind) .' );';

        $stmt = Db::getInstance()->prepare($sql);

        foreach ($values as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        if(!$stmt->execute()) {
            throw new Exception('Impossible d\'insérer dans ' . self::getTable(). ' avec les valeurs données.');
        }

        return Db::getInstance()->lastInsertId();
    }

}
