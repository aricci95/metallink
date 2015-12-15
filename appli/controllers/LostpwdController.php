<?php

class LostpwdController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_NONE;

    public function render()
    {
        if (!empty($this->params['value'])) {
            $this->_view->setViewName('lostPwd/wNewPwd');
            $this->_view->setTitle('Modification du mot de passe');
            $this->_view->pwd_valid = $this->params['value'];
        } else {
            $this->_view->setViewName('lostPwd/wLostpwd');
            $this->_view->setTitle('Récupération des identifiants');
        }

        $this->_view->render();
    }

    public function renderNew()
    {
        if (empty($this->params['value']) && empty($this->params['pwd_valid'])) {
            $this->_view->growlerError();
            $this->render();
        } else {
            $this->_view->setViewName('lostPwd/wNewPwd');
            $this->_view->setTitle('Modification du mot de passe');
            $this->_view->pwd_valid = empty($this->params['pwd_valid']) ? $this->params['value'] : $this->params['pwd_valid'];
            $this->_view->render();
        }
    }

    public function renderSubmit()
    {
        if (!empty($this->params['user_login'])) {
            if ($this->_model->Auth->sendPwd($this->params['user_login'])) {
                $this->redirect('home', array('msg' => MSG_PWD_SENT));
            } else {
                $this->_view->growler('Login / Email introuvable.', GROWLER_ERR);
                $this->render();
            }
        } elseif (!empty($this->params['user_mail'])) {
            if ($this->_model->Auth->sendPwd(null, $this->params['user_mail'])) {
                $this->redirect('home', array('msg' => MSG_PWD_SENT));
            } else {
                $this->_view->growler('Login / Email introuvable.', GROWLER_ERR);
                $this->render();
            }
        } else {
            $this->_view->growler('Login / Email introuvable.', GROWLER_ERR);
            $this->render();
        }
    }

    public function renderSubmitNew()
    {
        if (empty($this->params['pwd_valid'])
         || empty($this->params['user_pwd'])
         || empty($this->params['pwd_confirm'])
         || $this->params['user_pwd'] != $this->params['pwd_confirm']) {
            $this->_view->growler('Les deux champs doivent être identiques.', GROWLER_ERR);
            $this->renderNew();
        } else {
            if ($this->_model->Auth->updatePwd($this->params['user_pwd'], $this->params['pwd_valid'])) {
                $this->redirect('home', array('msg' => MSG_VALIDATION_PWD));
            } else {
                $this->_view->growlerError();
                $this->render();
            }
        }
    }
}
