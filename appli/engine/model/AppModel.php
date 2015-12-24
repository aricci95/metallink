<?php

abstract class AppModel
{

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

    public function load($model)
    {
        $model = ucfirst($model);
        if (!isset($this->$model)) {
            require_once ROOT_DIR.'/appli/models/'.$model.'.php';
            $this->$model = new $model();
        }

        return $this->$model;
    }
}
