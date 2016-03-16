<?php

abstract class Controller
{

    public $view;
    public $model;
    public $container;
    public $context;

    protected $_JS = array();

    public function __construct()
    {
        $this->context   = Context::getInstance();
        $this->model     = Model_Manager::getInstance();
        $this->container = Service_Container::getInstance();

        $this->view      = new AppView();

        $this->view->page   = (!empty($_GET['page'])) ? strtolower($_GET['page']) : 'home';
        $this->view->action = (!empty($_GET['action'])) ? strtolower($_GET['page']) : 'index';

        if (!empty($this->_JS)) {
            $this->addJSLibraries();
        }

        $this->context->buildParams();

        if (!empty($_GET['msg'])) {
            $this->showMessage();
        }
    }

    private function _setContext()
    {
        $init_vars = array(
            'id' => null,
            'user_login' => null,
            'last_connexion' => null,
            'role_id' => '',
            'photo_url' => 'unknowUser.jpg',
            'age' => null,
            'gender' => null,
        );

        $this->context = array_merge($_SESSION, $init_vars);
    }

    public function get($service)
    {
        return $this->container->get($service);
    }

    public function isAjax()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getView()
    {
        return $this->view;
    }

    public function addJSLibraries()
    {
        foreach ($this->_JS as $library) {
            $this->view->addJS($library);
        }
    }

    public function showMessage()
    {
        if (!empty($this->context->params['msg'])) {
            $msg = constant('MESSAGE_'.$this->context->params['msg']);
            if ($this->context->params['msg'] >= 400) {
                $type = GROWLER_ERR;
            } elseif ($this->context->params['msg'] >= 200 && $this->context->params['msg'] < 300) {
                $type = GROWLER_OK;
            } else {
                $type = GROWLER_INFO;
            }
            $this->view->growler($msg, $type);
        }
    }

    public function redirect($page = 'home', $params = null, $action = '')
    {
        $url = "/$page";
        if (!empty($action)) {
            $url .= '/'.$action;
        }
        if (is_array($params) && count($params > 0)) {
            foreach ($params as $key => $val) {
                if ($key === 'msg') {
                    $url .= '/'.$key.'/'.$val;
                } else {
                    $url .= '/'.$val;
                }
            }
        }
        header("Location: $url");
        die();
    }
}
