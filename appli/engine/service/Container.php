<?php

class Service_Container
{
    private $_services = array();

    private static $_instance = null;

    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

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

    private function _getAuthService()
    {
        $authService  = $this->_getService('auth');

        $mailerService = $this->getService('mailer');

        return $authService->requires($mailerService);
    }

    private function _getMessageService()
    {
        $messageService  = $this->_getService('message');

        $mailerService = $this->getService('mailer');

        return $messageService->requires($mailerService);
    }

    private function _getLinkService()
    {
        $linkService  = $this->_getService('link');

        $mailerService = $this->getService('mailer');

        return $linkService->requires($mailerService);
    }

}
