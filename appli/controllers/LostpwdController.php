<?php

class LostpwdController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_NONE;

    public function render()
    {
         $this->_view->setViewName('wLostpwd');
         $this->_view->setTitle('Récupération des identifiants');
         $this->_view->render();
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
}
