<?php

abstract class Model
{

    /**
     * [find description]
     * @param  string $table
     * @param  array  $attributes
     * @param  array  $where
     * @param  array $orderBy
     * @param  string $limit
     * @return array
     */
    protected static function _queryBuilder($table, $attributes_string = null, array $where = array(), array $orderBy = array(), $limit = null)
    {
        $sql = '
            SELECT
                ' . $attributes_string . '
            FROM
                ' . $table . '
            ';

        if (!empty($where)) {
            $sql .= ' WHERE TRUE ';

            foreach ($where as $key => $value) {
                if (strpos($key, '!') === 0) {
                    $key = str_replace('!', '', $key);
                    $sql .= " AND $key != :$key ";
                } else {
                    $sql .= " AND $key = :$key ";
                }
            }
        }

        if (!empty($orderBy)) {
            $sql .= ' ORDER BY ' . implode(',', $orderBy);
        }

        if (!empty($limit)) {
            $sql .= ' LIMIT ' . $limit;
        }

        $stmt = Db::getInstance()->prepare($sql);

        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $stmt->bindValue(str_replace('!', '', $key), $value);
            }
        }
        Log::debug($sql);
        if (!$stmt->execute()) {
            $error_message = $stmt->errorInfo();
            throw new Exception('La requête suivante : <b><br/>' . $sql . '</b><br/><br/>a renvoyé une erreur :<br/><i>' . $error_message[2] . '<i>', ERROR_SQL);
        }

        return $stmt->fetchAll();
    }

    public function fetch($sql)
    {
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function fetchOnly($sql)
    {
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function execute($sql, array $params = array())
    {
        $stmt = Db::getInstance()->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if (PROFILER) {
            $begin_time = microtime(true);
        }

        $response = $stmt->execute();

        if (PROFILER) {
            $end_time =  microtime(true);
            $executionTime = $end_time - $begin_time;

            if ($executionTime >= 0.0025) {
                echo '<div class="debug"><br/>' . $sql . '<br/><br/>' . $executionTime . '<br/></div>';
            }
        }

        if (!$response) {
            $error_message = $stmt->errorInfo();
            throw new Exception('La requête suivante : <b><br/>' . $sql . '</b><br/><br/>a renvoyé une erreur :<br/><i>' . $error_message[2] . '<i>', ERROR_SQL);
        };

        return $response;
    }

    public function insertId()
    {
        return Db::getInstance()->lastInsertId();
    }

    public function securize($data)
    {
        if (is_numeric($data)) {
            return $data;
        } else {
            return Db::getInstance()->real_escape_string(htmlentities($data, ENT_QUOTES));
        }
    }
}
