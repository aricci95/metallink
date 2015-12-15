<?php

abstract class AppModel extends EngineObject
{

    protected $_db;

    public function __construct($db = null)
    {
        parent::__construct();
        if (!empty($db)) {
            $this->_db = $db;
        }
    }

    public function fetch($sql)
    {
        return $this->_db->fetch($sql);
    }

    public function fetchOnly($sql)
    {
        return $this->_db->fetchOnly($sql);
    }

    public function execute($sql)
    {
        return $this->_db->execute($sql);
    }

    public function securize($val)
    {
        return $this->_db->securize($val);
    }

    public function insertId()
    {
        return $this->_db->insertId();
    }

    public function load($model)
    {
        $model = ucfirst($model);
        if (!isset($this->$model)) {
            require_once ROOT_DIR.'/appli/models/'.$model.'.php';
            $this->$model = new $model($this->_db);
        }
        return $this->$model;
    }
}
