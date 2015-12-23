<?php
require_once ROOT_DIR.'/appli/controllers/SearchController.php';

class CovoitController extends SearchController
{

    protected $_JS   = array(JS_COVOIT, JS_AUTOCOMPLETE, JS_DATEPICKER);
    protected $_type = 'Covoit';
    protected $_searchParams = array('search_concert',
                                     'search_ville');
    public function render()
    {
        parent::render();
        $this->_view->setTitle('Covoiturages');
        $this->_view->user = array('user_id' => User::getContextUser('id'),
                                  'user_login' => User::getContextUser('login'),
                                  'user_gender' => User::getContextUser('gender'),
                                  'user_photo_url' => User::getContextUser('photo_url'),
                                  'user_last_connexion' => time(),
                                  'user_statut' => User::getContextUser('status')
                                  );
        $this->_view->render();
    }

    public function renderSave()
    {
      // On formate les dates
        $depart = DateTime::createFromFormat('d/m/Y H:i', $this->params['date_depart']);
        $retour = DateTime::createFromFormat('d/m/Y H:i', $this->params['date_retour']);
        $this->params['date_depart'] = $depart->format("Y-m-d H:i");
        $this->params['date_retour'] = $retour->format("Y-m-d H:i");
        if ($this->_model->covoit->create($this->params)) {
            $this->redirect('covoit', array('msg' => MSG_COVOIT_OK));
        } else {
            $this->redirect('covoit', array('msg' => ERR_DEFAULT));
        }
    }

    public function renderDelete()
    {
        return $this->_model->covoit->deleteById($this->params['value']);
    }

    public function renderMore()
    {
        parent::renderMore();
        $this->_view->getJSONResponse('covoit/wItems');
    }
}
