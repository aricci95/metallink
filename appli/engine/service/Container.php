<?php

class Container
{
    protected $_services = array();

    public function getService($service)
    {
        if (!empty($this->_service[$service])) {
           return $this->_service[$service];
        }

        $serviceClassName = ucfirst($service) . 'Service';

        $serviceFilePath = ROOT_DIR . '/appli/services/' . $serviceClassName . '.php';
        $modelFilePath   = ROOT_DIR . '/appli/models/' . ucfirst($service) . '.php';

        if (!file_exists($serviceFilePath)) {
            throw new Exception('Service "'. $service .'" introuvable.', ERROR_NOT_FOUND);
        }

        try {
            require $serviceFilePath;

            if (file_exists($modelFilePath)) {
                require_once $modelFilePath;
            }

            $this->_services[$service] = new $serviceClassName();
        } catch (Exception $e) {
            throw new Exception("Impossible d'instancier $serviceClassName");
        }

        return $this->_services[$service];
    }
}
