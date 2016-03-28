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
        $this->view->addJS(JS_MODAL);
        $this->view->new = true;
        parent::render();
    }

    public function renderSave()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($this->context->getParam('annonce_title'))) {
            $annonceData = array(
                'annonce_title' => htmlspecialchars(trim($this->context->getParam('annonce_title')), ENT_QUOTES, 'utf-8'),
                'annonce_content' => htmlspecialchars(trim($this->context->getParam('annonce_content')), ENT_QUOTES, 'utf-8'),
                'user_id' => $this->context->get('user_id'),
                'annonce_date' => time(),
            );

            $annonceId = $this->model->annonce->insert($annonceData);

            if (!empty($_FILES['photo']['tmp_name'])) {
                $this->get('photo')->uploadImage($_FILES['photo']['name'], $_FILES['photo']['tmp_name'], PHOTO_TYPE_ANNONCE, $annonceId);
            }

            $this->redirect('annonce', array('msg' => MSG_ANNONCE_OK));
            $this->render();
        }
    }
}