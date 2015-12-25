<?php

abstract class AppModel extends Model
{

    private static $_table;

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
}
