<?php

abstract class SearchController extends AppController
{

    protected $_searchParams = array();
    protected $_type;

    public function render()
    {
        $criterias              = $this->_getSearchCriterias();
        $this->view->addJS(JS_SCROLL_REFRESH);
        $this->view->type      = $this->_type;
        $this->view->criterias = $criterias;
        $this->view->elements  = $this->model->{$this->_type}->getSearch($criterias);
        $this->view->offset    = 1;
        $this->view->setViewName(strtolower($this->_type).'/wList');
    }

    public function renderMore()
    {
        $offset                = $this->params['value'];
        $criterias             = $this->_getSearchCriterias();
        $this->view->elements = $this->model->{$this->_type}->getSearch($criterias, $offset);
        $this->view->type     = $this->_type;
        $this->view->offset   = $offset++;
    }

    private function _getSearchCriterias()
    {
        foreach ($this->_searchParams as $param) {
            if (isset($this->params[$param])) {
                $_SESSION[$param] = $this->params[$param];
            }
            $datas[$param] = (!empty($_SESSION[$param])) ? $_SESSION[$param]: '';
        }
        return $datas;
    }
}
