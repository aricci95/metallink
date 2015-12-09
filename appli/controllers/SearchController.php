<?php

abstract class SearchController extends AppController
{

    protected $_searchParams = array();
    protected $_type;

    public function render()
    {
        $criterias              = $this->_getSearchCriterias();
        $this->_view->addJS(JS_SCROLL_REFRESH);
        $this->_view->type      = $this->_type;
        $this->_view->criterias = $criterias;
        $this->_view->elements  = $this->_model->{$this->_type}->getSearch($criterias);
        $this->_view->offset    = 1;
        $this->_view->setViewName(strtolower($this->_type).'/wList');
    }

    public function renderMore()
    {
        $offset                = $this->params['value'];
        $criterias             = $this->_getSearchCriterias();
        $this->_view->elements = $this->_model->{$this->_type}->getSearch($criterias, $offset);
        $this->_view->type     = $this->_type;
        $this->_view->offset   = $offset++;
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
