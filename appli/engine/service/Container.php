<?php

class Container
{
    private $_services = array();

    public function getService($serviceName)
    {
        if (!empty($this->_services[$serviceName])) {
           return $this->_services[$serviceName];
        }

        $methodName = '_get' . ucfirst($serviceName) . 'Service';

        $this->_services[$serviceName] = method_exists($this, $methodName) ? $this->$methodName() : $this->_getService($serviceName);

        return $this->_services[$serviceName];
    }

    private function _getService($serviceName)
    {
        if (!empty($this->_services[$serviceName])) {
           return $this->_services[$serviceName];
        }

        $serviceClassName = ucfirst($serviceName) . 'Service';

        $serviceFilePath = ROOT_DIR . '/appli/services/' . $serviceClassName . '.php';

        if (!file_exists($serviceFilePath)) {
            throw new Exception('Service "'. $serviceName .'" introuvable.', ERROR_NOT_FOUND);
        }

        try {
            require $serviceFilePath;

            $service = new $serviceClassName();
        } catch (Exception $e) {
            throw new Exception("Impossible d'instancier $serviceClassName");
        }

        return $service;
    }

    private function _getUserService()
    {
        $userService  = $this->_getService('user');

        $photoService = $this->getService('photo');

        return $userService->requires($photoService);
    }

}
