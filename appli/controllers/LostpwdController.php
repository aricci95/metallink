<?php

class LostpwdController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_NONE;

    public function render()
    {
        if (!empty($this->context->params['value'])) {
            $this->view->setViewName('lostPwd/wNewPwd');
            $this->view->setTitle('Modification du mot de passe');
            $this->view->pwd_valid = $this->context->params['value'];
        } else {
            $this->view->setViewName('lostPwd/wLostPwd');
            $this->view->setTitle('Récupération des identifiants');
        }

        $this->view->render();
    }

    public function renderNew()
    {
        if (empty($this->context->params['value']) && empty($this->context->params['pwd_valid'])) {
            $this->view->growlerError();
            $this->render();
        } else {
            $this->view->setViewName('lostPwd/wNewPwd');
            $this->view->setTitle('Modification du mot de passe');
            $this->view->pwd_valid = empty($this->context->params['pwd_valid']) ? $this->context->params['value'] : $this->context->params['pwd_valid'];
            $this->view->render();
        }
    }

    public function renderSubmit()
    {
        if (!empty($this->context->params['user_login'])) {
            if ($this->get('auth')->sendPwd($this->context->params['user_login'])) {
                $this->redirect('user', array('msg' => MSG_PWD_SENT));
            } else {
                $this->view->growler('Login / Email introuvable.', GROWLER_ERR);
                $this->render();
            }
        } elseif (!empty($this->context->params['user_mail'])) {
            if ($this->get('auth')->sendPwd(null, $this->context->params['user_mail'])) {
                $this->redirect('user', array('msg' => MSG_PWD_SENT));
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
        if (empty($this->context->params['pwd_valid'])
         || empty($this->context->params['user_pwd'])
         || empty($this->context->params['pwd_confirm'])
         || $this->context->params['user_pwd'] != $this->context->params['pwd_confirm']) {
            $this->view->growler('Les deux champs doivent être identiques.', GROWLER_ERR);
            $this->renderNew();
        } else {
            if ($this->model->auth->updatePwd($this->context->params['user_pwd'], $this->context->params['pwd_valid'])) {
                $this->redirect('user', array('msg' => MSG_VALIDATION_PWD));
            } else {
                $this->view->growlerError();
                $this->render();
            }
        }
    }
}
