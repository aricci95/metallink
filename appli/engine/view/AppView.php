<?php

class AppView
{

    private $_name;
    private $_title;
    protected $_html;
    protected $_JSLibraries     = array();
    protected $_growlerMessages = array();
    public $userStatuses     = array();

    public function __construct()
    {
        $this->_helper = new ViewHelper();
        $this->headerImg = $this->_getHeaderImage();
    }

    public function setHelperDatas(array $datas)
    {
        foreach ($datas as $key => $data) {
            $this->_helper->$key = $data;
        }
    }

    private function _getHeaderImage()
    {
        $date  = date('m');
        $image = 'MLink/images/structure/';
        if ($date <= 12 && $date >= 10) {
            $image .= 'headernoel.jpg';
        } else {
            $image .= 'header.jpg';
        }
        return $image;
    }

    public function setViewName($name = '')
    {
        $this->_name = $name;
    }

    public function setTitle($title = '')
    {
        $this->_title = $title;
    }

    public function getPage()
    {
        return (!empty($_GET['page'])) ? $_GET['page'] : 'home';
    }

    public function getViewFileName()
    {
        return 'views/'.$this->_name.'.php';
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setViewDatas($datas = array())
    {
        foreach ($datas as $key => $val) {
            echo $key;
            $this->$key = $val;
        }
    }

    public function addJS($library)
    {
        $this->_JSLibraries[$library] = true;
    }

    public function isJSActivated($library)
    {
        if (array_key_exists($library, $this->_JSLibraries) && $this->_JSLibraries[$library]) {
            return true;
        } else {
            return false;
        }
    }

    public function render($view = null, $datas = array())
    {
        if (count($datas) > 0) {
            foreach ($datas as $key => $data) {
                $this->$key = $data;
            }
        }
        echo $this->runView($view);
    }

    public function runView($view = null)
    {
        ob_start();
        if (empty($view)) {
            include('views/view.php');
        } else {
            include('views/'.$view.'.php');
        }
        return ob_get_clean();
    }

    public function getJSONResponse($view)
    {
        echo $this->runView($view);
    }

    public function growler($message = MESSAGE_400, $type = GROWLER_ERR, $title = null)
    {
        if (!$this->isJSActivated(JS_GROWLER)) {
            $this->addJS(JS_GROWLER);
        }

        $script_message = "<script>
            $(function(){
                $.gritter.add({
            ";

        if (!empty($title)) {
            $script_message .= "title: '$title',";
        }

        $script_message .= "
                    text:  '$message',
                    class_name : 'gritter-".$type."'
                });
            });
            </script>";

        $this->_growlerMessages[] = $script_message;
    }

    public function growlerError()
    {
        if (!$this->isJSActivated(JS_GROWLER)) {
            $this->addJS(JS_GROWLER);
        }

        $this->_growlerMessages[] = "<script>
            $(function(){
                $.gritter.add({
                    text:  '".MESSAGE_400."',
                    class_name : 'gritter-err'
                });
            });
            </script>";
    }
}
