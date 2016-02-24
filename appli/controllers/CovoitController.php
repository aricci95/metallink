<?php

require_once ROOT_DIR . '/appli/controllers/SearchController.php';

class CovoitController extends SearchController
{

    protected $_JS = array(
        JS_COVOIT,
        JS_AUTOCOMPLETE,
        JS_DATEPICKER,
    );

    protected $_type = 'Covoit';
    protected $_searchParams = array('search_concert', 'search_ville');

    public function render()
    {
        parent::render();

        $this->view->setTitle('Covoiturages');

        $this->view->user = array(
            'user_id' => $this->context->get('user_id'),
            'user_login' => $this->context->get('user_login'),
            'user_gender' => $this->context->get('user_gender'),
            'user_photo_url' => $this->context->get('user_photo_url'),
            'user_last_connexion' => time(),
        );

        $this->view->render();
    }

    public function renderSave()
    {
        if (empty($this->context->params['date_depart']) || empty($this->context->params['date_retour']) || empty($this->context->params['concert_id']) || empty($this->context->params['ville_id']) || empty($this->context->params['price'])) {
            $this->view->growler('Veuillez renseigner tous les champs.', GROWLER_INFO);
            $this->render();
            exit;
        }

        // On formate les dates
        $depart = DateTime::createFromFormat('d/m/Y H:i', $this->context->params['date_depart']);
        $retour = DateTime::createFromFormat('d/m/Y H:i', $this->context->params['date_retour']);

        $covoit_data = array(
            'price' => (int) $this->context->params['price'],
            'concert_id' => (int) $this->context->params['concert_id'],
            'ville_id' => (int) $this->context->params['ville_id'],
            'date_depart' => (string) $depart->format("Y-m-d H:i"),
            'date_retour' => (string) $retour->format("Y-m-d H:i"),
            'user_id' => (int) $this->context->get('user_id'),
        );

        if ($this->model->covoit->insert($covoit_data)) {
            $this->redirect('covoit', array('msg' => MSG_COVOIT_OK));
        } else {
            $this->redirect('covoit', array('msg' => ERR_DEFAULT));
        }
    }

    public function renderDelete()
    {
        return $this->model->covoit->deleteById($this->context->params['value']);
    }

    public function renderMore()
    {
        parent::renderMore();

        $this->view->getJSONResponse('covoit/wItems');
    }
}
