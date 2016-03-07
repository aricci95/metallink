<?php

abstract class SearchController extends AppController
{
    protected $_type;

    protected $_searchParams = array();

    public function __construct()
    {
        parent::__construct();

        $this->view->type = $this->_type;
    }

    public function render()
    {
        $this->view->addJS(JS_SCROLL_REFRESH);
        $this->view->addJS(JS_SEARCH);
        $this->view->addJS(JS_ARTICLE);

        $criterias             = $this->_getSearchCriterias();
        $this->view->criterias = $criterias;
        $this->view->elements  = $this->model->{$this->_type}->getSearch($criterias);

        $this->view->setTitle('Recherche');
        $this->view->setViewName('search/wMain');

        $this->view->render();
    }

    public function renderGetResults()
    {
        $criterias             = $this->_getSearchCriterias();
        $this->view->criterias = $criterias;
        $this->view->elements  = $this->model->{$this->_type}->getSearch($criterias);

        $this->view->getJSONResponse('search/wList');
    }

    public function renderMore()
    {
        $offset                = $this->context->getParam('value');
        $criterias             = $this->_getSearchCriterias();
        $this->view->criterias = $criterias;
        $this->view->elements  = $this->model->{$this->_type}->getSearch($criterias, $offset);

        if (!empty($this->view->elements)) {
            $this->view->offset   = $offset++;
            $this->view->getJSONResponse('search/wList');
        }
    }

    private function _getSearchCriterias()
    {
        foreach ($this->_searchParams as $param) {
            if (isset($this->context->params[$param])) {
                $this->context->set($param, $this->context->params[$param]);
            }

            $datas[$param] = $this->context->get($param);
        }
        return $datas;
    }
}
