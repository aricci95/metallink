<?php

class LostpwdController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_NONE;

    public function render()
    {
        if (!empty($this->params['value'])) {
            $this->view->setViewName('lostPwd/wNewPwd');
            $this->view->setTitle('Modification du mot de passe');
            $this->view->pwd_valid = $this->params['value'];
        } else {
            $this->view->setViewName('lostPwd/wLostPwd');
            $this->view->setTitle('Récupération des identifiants');
        }

        $this->view->render();
    }

    public function renderNew()
    {
        if (empty($this->params['value']) && empty($this->params['pwd_valid'])) {
            $this->view->growlerError();
            $this->render();
        } else {
            $this->view->setViewName('lostPwd/wNewPwd');
            $this->view->setTitle('Modification du mot de passe');
            $this->view->pwd_valid = empty($this->params['pwd_valid']) ? $this->params['value'] : $this->params['pwd_valid'];
            $this->view->render();
        }
    }

    public function renderSubmit()
    {
        if (!empty($this->params['user_login'])) {
            if ($this->model->Auth->sendPwd($this->params['user_login'])) {
                $this->redirect('home', array('msg' => MSG_PWD_SENT));
            } else {
                $this->view->growler('Login / Email introuvable.', GROWLER_ERR);
                $this->render();
            }
        } elseif (!empty($this->params['user_mail'])) {
            if ($this->model->Auth->sendPwd(null, $this->params['user_mail'])) {
                $this->redirect('home', array('msg' => MSG_PWD_SENT));
            } else {
                $this->view->growler('Login / Email introuvable.', GROWLER_ERR);
                $this->render();
            }
        } else {
            $this->view->growler('Login / Email introuvable.', GROWLER_ERR);
            $this->render();
        }
    }

    public function renderSubmitNew()
    {
        if (empty($this->params['pwd_valid'])
         || empty($this->params['user_pwd'])
         || empty($this->params['pwd_confirm'])
         || $this->params['user_pwd'] != $this->params['pwd_confirm']) {
            $this->view->growler('Les deux champs doivent être identiques.', GROWLER_ERR);
            $this->renderNew();
        } else {
            if ($this->model->Auth->updatePwd($this->params['user_pwd'], $this->params['pwd_valid'])) {
                $this->redirect('home', array('msg' => MSG_VALIDATION_PWD));
            } else {
                $this->view->growlerError();
                $this->render();
            }
        }
    }
}
