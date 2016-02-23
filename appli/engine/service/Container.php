<?php

class Service_Container
{
    private static $_instance = null;

    private $_services = array();

    private $_dependencies = array(
        'Auth'    => array('Mailer'),
        'Link'    => array('Mailer'),
        'Message' => array('Mailer'),
        'User'    => array('Photo'),
    );

    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function get($serviceName)
    {
        $serviceName = ucfirst($serviceName);

        $service = $this->_load($serviceName);

        if (!empty($this->_dependencies[$serviceName])) {
            foreach ($this->_dependencies[$serviceName] as $dependenceServiceName) {
                $dependenceService = $this->_load($dependenceServiceName);

                $service->requires($dependenceService);
            }
        }

        return $this->_services[$serviceName];
    }

    private function _load($serviceName)
    {
        if (!empty($this->_services[$serviceName])) {
           return $this->_services[$serviceName];
        }

        $serviceClassName = $serviceName . 'Service';

        $serviceFilePath = ROOT_DIR . '/appli/services/' . $serviceClassName . '.php';

        if (!file_exists($serviceFilePath)) {
            throw new Exception('Service "'. $serviceName .'" introuvable.', ERROR_NOT_FOUND);
        }

        try {
            require $serviceFilePath;

            $this->_services[$serviceName] = new $serviceClassName();
        } catch (Exception $e) {
            throw new Exception("Impossible d'instancier $serviceClassName");
        }

        return $this->_services[$serviceName];
    }
}
