<?php

class Model_Manager extends Model
{

    private $_models = array();

    public function __get($model)
    {
        $lowerName = strtolower($model);

        if (!isset($this->_models[$lowerName])) {
            $this->_models[$lowerName] = $this->load($lowerName);
        }

        return $this->_models[$lowerName];
    }

    public function load($model)
    {
        $model     = ucfirst($model);
        $lowerName = strtolower($model);
        $filePath  = ROOT_DIR . '/appli/models/' . $model . '.php';

        if (!file_exists($filePath)) {
            throw new Exception('Model "'. $model .'" introuvable.', ERROR_NOT_FOUND);
        }

        require_once $filePath;
        $this->_models[$model] = new $model();

        return $this->_models[$model];
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
}
