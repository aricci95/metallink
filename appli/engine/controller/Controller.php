<?php
abstract class Controller
{

    public $params = array();
    public $view;
    public $model;

    protected $_JS = array();

    public function __construct()
    {
        $this->model = new Model_Manager();
        $this->view = new AppView();

        $this->view->page   = (!empty($_GET['page'])) ? strtolower($_GET['page']) : 'home';
        $this->view->action = (!empty($_GET['action'])) ? strtolower($_GET['page']) : 'index';

        if (!empty($this->_JS)) {
            $this->addJSLibraries();
        }

        $this->_buildParams();

        if (!empty($_GET['msg'])) {
            $this->showMessage();
        }
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

    private function _buildParams()
    {
        unset($_GET['page']);
        unset($_GET['action']);
        unset($_POST['x']);
        unset($_POST['y']);

        if (!empty($_GET)) {
            foreach ($_GET as $key => $value) {
                if (!is_array($value)) {
                    $value = trim($value);
                }
                $this->params[$key] = $value;
            }
        }

        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                if (!is_array($value)) {
                    $value = trim($value);
                }
                $this->params[$key] = $value;
            }
        }

        return $this->params;
    }

    public function addJSLibraries()
    {
        foreach ($this->_JS as $library) {
            $this->view->addJS($library);
        }
    }

    public function showMessage()
    {
        if (!empty($this->params['msg'])) {
            $msg = constant('MESSAGE_'.$this->params['msg']);
            if ($this->params['msg'] >= 400) {
                $type = GROWLER_ERR;
            } elseif ($this->params['msg'] >= 200 && $this->params['msg'] < 300) {
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
