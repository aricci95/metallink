<?php

Abstract Class Service
{
    protected $_dependencies = array();

    public $name;

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
