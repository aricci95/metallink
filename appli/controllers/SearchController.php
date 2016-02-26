<?php

class SearchController extends AppController
{
    private $_type;

    private $_searchParams = array(
        SEARCH_TYPE_USER => array(
            'search_login',
            'search_type',
            'search_distance',
            'search_gender',
            'search_age',
        ),

        SEARCH_TYPE_CONCERT => array(
            'search_location',
        ),
        SEARCH_TYPE_ARTICLE => array(
            'search_libel',
            'search_type',
            'search_categorie',
        ),
    );

    public function __construct()
    {
        parent::__construct();

        $param = $this->context->getParam('type');

        $this->_type = !empty($param) ? $param : SEARCH_TYPE_USER;

        $this->view->type = $this->_type;
    }

    public function render()
    {
        $this->view->addJS(JS_SCROLL_REFRESH);
        $this->view->addJS(JS_SEARCH);

        $criterias             = $this->_getSearchCriterias();
        $this->view->criterias = $criterias;
        $this->view->elements  = $this->model->{$this->_type}->getSearch($criterias);

        $this->view->setTitle('Recherche');
        $this->view->setViewName('search/wMain');

        $this->view->render();
    }

    public function renderCriterias()
    {
        $this->view->criterias = $this->_getSearchCriterias();
        $this->view->getJSONResponse('search/w' . $this->_type);
    }

    public function renderGetResults()
    {
        $criterias             = $this->_getSearchCriterias();
        $this->view->criterias = $criterias;
        $this->view->elements  = $this->model->{$this->_type}->getSearch($criterias);

        $this->view->getJSONResponse(strtolower($this->_type) . '/wList');
    }

    public function renderMore()
    {
        $offset                = $this->context->getParam('value');
        $criterias             = $this->_getSearchCriterias();
        $this->view->criterias = $criterias;
        $this->view->elements  = $this->model->{$this->_type}->getSearch($criterias, $offset);

        $this->view->offset   = $offset++;
        $this->view->getJSONResponse(strtolower($this->_type) . '/wList');
    }

    private function _getSearchCriterias()
    {
        foreach ($this->_searchParams[$this->_type] as $param) {
            if (isset($this->context->params[$param])) {
                $this->context->set($param, $this->context->params[$param]);
            }

            $datas[$param] = $this->context->get($param);
        }
        return $datas;
    }
}
