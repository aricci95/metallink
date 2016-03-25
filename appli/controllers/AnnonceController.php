<?php

require_once ROOT_DIR . '/appli/controllers/SearchController.php';

class AnnonceController extends SearchController
{
    protected $_type = SEARCH_TYPE_ANNONCE;

    protected $_searchParams = array(
        'search_keyword',
        'search_distance',
    );

    public function render()
    {
        $this->view->addJS(JS_ANNONCE);
        $this->view->new = true;
        parent::render();
    }
}