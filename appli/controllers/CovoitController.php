<?php

require_once ROOT_DIR.'/appli/controllers/SearchController.php';

class CovoitController extends SearchController
{

    protected $_JS   = array(JS_COVOIT, JS_AUTOCOMPLETE, JS_DATEPICKER);
    protected $_type = 'Covoit';
    protected $_searchParams = array('search_concert', 'search_ville');

    public function render()
    {
        parent::render();

        $this->view->setTitle('Covoiturages');
        $this->view->user = array(
            'user_id' => User::getContextUser('id'),
            'user_login' => User::getContextUser('login'),
            'user_gender' => User::getContextUser('gender'),
            'user_photo_url' => User::getContextUser('photo_url'),
            'user_last_connexion' => time(),
            'user_statut' => User::getContextUser('status'),
        );

        $this->view->render();
    }

    public function renderSave()
    {
        if (empty($this->params['date_depart']) || empty($this->params['date_retour']) || empty($this->params['concert_id']) || empty($this->params['ville_id']) || empty($this->params['price'])) {
            $this->view->growler('Veuillez renseigner tous les champs.', GROWLER_INFO);
            $this->render();
            exit;
        }

        // On formate les dates
        $depart = DateTime::createFromFormat('d/m/Y H:i', $this->params['date_depart']);
        $retour = DateTime::createFromFormat('d/m/Y H:i', $this->params['date_retour']);

        $covoit_data = array(
            'price' => (int) $this->params['price'],
            'concert_id' => (int) $this->params['concert_id'],
            'ville_id' => (int) $this->params['ville_id'],
            'date_depart' => (string) $depart->format("Y-m-d H:i"),
            'date_retour' => (string) $retour->format("Y-m-d H:i"),
            'user_id' => (int) User::getContextUser('id'),
        );

        if (Covoit::insert($covoit_data)) {
            $this->redirect('covoit', array('msg' => MSG_COVOIT_OK));
        } else {
            $this->redirect('covoit', array('msg' => ERR_DEFAULT));
        }
    }

    public function renderDelete()
    {
        return Covoit::deleteById($this->params['value']);
    }

    public function renderMore()
    {
        parent::renderMore();

        $this->view->getJSONResponse('covoit/wItems');
    }
}
