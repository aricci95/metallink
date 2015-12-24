<?php

class Model extends AppModel
{

    public function __get($value)
    {
        return $this->load($value);
    }

    public function load($model)
    {
        $model     = ucfirst($model);
        $lowerName = strtolower($model);
        $filePath  = ROOT_DIR.'/appli/models/'.$model.'.php';

        if (!isset($this->$model) && !isset($this->$lowerName)) {
            if (!file_exists($filePath)) {
                throw new Exception('Model "'. $model .'" introuvable.', ERROR_NOT_FOUND);
            }
            require_once $filePath;
            $this->$model = new $model();
        }
        return $this->$model;
    }

    public static function count($table, array $where = array(), array $orderBy = array(), $limit = null)
    {
        $attributes_string = 'count(*) AS counter';

        $data = self::_queryBuilder($table, $attributes_string, $where, $orderBy, $limit);

        return (int) $data[0]['counter'];
    }

    public static function find($table, array $attributes = array(), array $where = array(), array $orderBy = array(), $limit = null)
    {
        $attributes_string = empty($attributes) ? '*' : implode(',', $attributes);

        return self::_queryBuilder($table, $attributes_string, $where, $orderBy, $limit);
    }

    /**
     * [find description]
     * @param  string $table
     * @param  array  $attributes
     * @param  array  $where
     * @param  array $orderBy
     * @param  string $limit
     * @return array
     */
    private static function _queryBuilder($table, $attributes_string = null, array $where = array(), array $orderBy = array(), $limit = null)
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

        $stmt->execute();

        return $stmt->fetchAll();

    }

   // Liste une table
    public function getItemsFromTable($table, $order = false)
    {
        $type = str_replace("ref_", "", $table);

        if ($order == false) {
            $libel = $type.'_libel';
        } else {
            $libel = $order;
        }

        $sql = "SELECT * FROM $table ORDER BY $libel";
        $datas = $this->fetch($sql);
        return $datas;
    }

    public function hasSpecialChar($chaine)
    {
        $specialChars = array(
        'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a',
        'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e',
        'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
        'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u',
        'Œ' => 'oe', 'œ' => 'oe',
        '$' => 's');
        $compareString = strtr($chaine, $specialChars);
        $compareString = preg_replace('#[^A-Za-z0-9]+#', '-', $compareString);
        return ($compareString != $chaine);
    }
}
