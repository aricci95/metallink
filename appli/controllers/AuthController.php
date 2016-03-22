<?php
class AuthController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_NONE;

    public function renderLogin()
    {
        if (!empty($this->context->params['user_login']) && !empty($this->context->params['user_pwd'])) {
            try {
                $authentResult = $this->get('auth')->login($this->context->params['user_login'], $this->context->params['user_pwd']);

                if ($authentResult) {
                    $this->redirect('user');
                }
            } catch (Exception $e) {
                Log::err($e->getMessage());
                $this->redirect('user', array('msg' => $e->getCode()));
            }
        }

        $this->redirect('user', array('msg' => ERR_LOGIN));
    }

    public function renderDisconnect()
    {
        if ($this->get('auth')->disconnect()) {
            $this->redirect('user');
        } else {
            $this->view->growlerError();
        }
    }
}
