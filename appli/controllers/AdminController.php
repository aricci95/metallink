<?php

class AdminController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_SUPERADMIN;

    public function render()
    {
        $this->view->setTitle('Administration');
        $this->view->setViewName('admin/wAdmin');
        $this->view->render();
    }

    public function renderSwitch()
    {
        $this->view->users  = User::find(array(), array('!user_id' => User::getContextUser('id'), 'user_valid' => 1),  array('user_login'));
        $this->view->action = 'setSwitch';
        $this->view->setTitle('User switch');
        $this->view->setViewName('admin/wUsers');
        $this->view->render();
    }

    public function renderSetSwitch()
    {
        if (!empty($this->params['user_id'])) {
            $user = User::findById($this->params['user_id']);

            if (!empty($user)) {
                if ($user['user_valid'] != 1) {
                    $this->view->growler('Utilistateur non validé.', GROWLER_ERR);
                } else {
                    $_SESSION['user_id']            = $user['user_id'];
                    $_SESSION['user_login']         = $user['user_login'];
                    $_SESSION['role_id']            = $user['role_id'];
                    $_SESSION['user_photo_url']     = $user['user_photo_url'];
                    $_SESSION['user_valid']         = $user['user_valid'];
                    $_SESSION['user_mail']          = $user['user_mail'];
                    $_SESSION['user_gender']        = $user['user_gender'];
                    $_SESSION['forum_notification'] = $user['forum_notification'];

                    $this->redirect('home', array('msg' => MSG_ADM_SWITCH));
                }
            }
        } else {
            $this->view->growlerError();
        }
        $this->render();
    }

    public function renderDeleteUser()
    {
        $this->view->users  = User::find(array('user_id', 'user_login'), array('!user_id' => User::getContextUser('id')));
        $this->view->action = 'removeUser';
        $this->view->setTitle('Supprimer un utilisateur');
        $this->view->setViewName('admin/wUsers');
        $this->view->render();
    }

    public function renderRemoveUser()
    {
        if (!empty($this->params['user_id']) && $this->get('user')->delete($this->params['user_id'])) {
            $this->view->growler('Utilisateur supprimé.', GROWLER_OK);
        } else {
            $this->view->growlerError();
        }

        $this->render();
    }

    public function renderMessage()
    {
        $this->view->setTitle('Message à tous les users');
        $this->view->setViewName('admin/wMessage');
        $this->view->render();
    }

    public function renderMessageSubmit()
    {
        if (!empty($this->params['content'])) {
            $from    = User::getContextUser('id');
            $users   = User::find(array('user_id'), array('!user_id' => User::getContextUser('id')));

            $sentMessages = 0;
            foreach ($users as $user) {
                if ($this->get('message')->send($from, $user['user_id'], $this->params['content'])) {
                    $sentMessages++;
                }
            }

            if ($sentMessages > 0) {
                $this->view->growler($sentMessages.' Emails envoyés.', GROWLER_OK);
            } else {
                $this->view->growlerError();
            }
        } else {
            $this->view->growlerError('Le message vide.');
        }

        $this->render();
    }
}
