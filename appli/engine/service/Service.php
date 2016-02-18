<?php

Abstract Class Service extends AppModel
{
    protected $_dependencies = array();

    public $name;
    public $model;
    public $context;

    public function __construct()
    {
        $this->context = Context::getInstance();
        $this->model   = Model_Manager::getInstance();
    }

    public function get($service)
    {
        return $this->_dependencies[strtolower($service)];
    }

    public function getName()
    {
        if (empty($this->name)) {
            $this->name = strtolower(str_replace('Service', '', get_called_class()));
        }

        return $this->name;
    }

    public function requires(Service $dependenceService)
    {
        $this->_dependencies[$dependenceService->getName()] = $dependenceService;

        return $this;
    }
}
