<?php
require_once ROOT_DIR.'/appli/controllers/SearchController.php';

class SalesController extends SearchController
{

    protected $_JS   = array(JS_SCROLL_REFRESH, JS_ARTICLE);
    protected $_type = 'Article';
    protected $_searchParams = array('search_libel',
                                     'search_categorie');

    public function render()
    {
        parent::render();
        $this->_view->setTitle('Ventes');
        $this->_view->categories = $this->_model->Article->getCategories();
        $this->_view->render();
    }

    public function renderMore()
    {
        parent::renderMore();
        $this->_view->getJSONResponse('article/wItems');
    }
}
