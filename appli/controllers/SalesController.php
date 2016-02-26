<?php
require_once ROOT_DIR.'/appli/controllers/SearchController.php';

class SalesController extends SearchController
{

    protected $_JS   = array(JS_SCROLL_REFRESH, JS_ARTICLE);
    protected $_type = 'Article';
    protected $_searchParams = array('search_libel',
                                     'search_type',
                                     'search_categorie');

    public function render()
    {
        parent::render();
        $this->view->setTitle('Ventes');
        $this->view->categories = $this->model->Article->getCategories();
        $this->view->render();
    }

    public function renderMore()
    {
        parent::renderMore();
        $this->view->getJSONResponse('article/wItems');
    }
}
