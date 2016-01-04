<?php

abstract class AppModel extends Model
{

    private static $_table;
    private static $_primary;

    public static function count(array $where = array(), array $orderBy = array(), $limit = null)
    {
        if (empty(self::$_table)) {
            self::$_table = strtolower(get_called_class());
        }

        $attributes_string = 'count(*) AS counter';

        $data = self::_queryBuilder(self::$_table, $attributes_string, $where, $orderBy, $limit);

        return (int) $data[0]['counter'];
    }

    public static function find(array $attributes = array(), array $where = array(), array $orderBy = array(), $limit = null)
    {
        if (empty(self::$_table)) {
            self::$_table = strtolower(get_called_class());
        }

        $attributes_string = empty($attributes) ? '*' : implode(',', $attributes);

        return self::_queryBuilder(self::$_table, $attributes_string, $where, $orderBy, $limit);
    }

    public static function findById($id, array $attributes = array(), array $orderBy = array(), $limit = null)
    {
        if (empty(self::$_table)) {
            self::$_table = strtolower(get_called_class());
        }

        if (empty(self::$_primary)) {
            self::$_primary = self::$_table . '_id';
        }

        $attributes_string = empty($attributes) ? '*' : implode(',', $attributes);

        $where = array(
            self::$_primary => (int) $id,
        );

        $results = self::_queryBuilder(self::$_table, $attributes_string, $where, $orderBy, $limit);

        return $results[0];
    }

    public static function deleteById($id)
    {
        $sql = 'DELETE FROM :table WHERE :primary = :id';

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue('table', self::$_table);
        $stmt->bindValue('primary', self::$_primary);
        $stmt->bindValue('id', (int) $id);

        return $stmt->execute();
    }

}
