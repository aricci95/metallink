<?php
require_once ROOT_DIR.'/appli/controllers/SearchController.php';

class UserController extends SearchController
{

    protected $_type         = 'User';
    protected $_searchParams = array('search_login',
                                     'search_distance',
                                     'search_gender',
                                     'search_age');

    public function render()
    {
        parent::render();
        $this->view->setTitle('Recherche');
        $this->view->render();
    }

    public function renderMore()
    {
        parent::renderMore();
        $this->view->getJSONResponse('user/wItems');
    }
}
