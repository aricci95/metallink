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
        $this->view->users  = User::find(array(), array('!user_id' => User::getContextUser('id')));
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
                    $_SESSION['user_id']        = $user['user_id'];
                    $_SESSION['user_login']     = $user['user_login'];
                    $_SESSION['role_id']        = $user['role_id'];
                    $_SESSION['user_photo_url'] = $user['user_photo_url'];
                    $_SESSION['user_valid']     = $user['user_valid'];
                    $_SESSION['user_mail']      = $user['user_mail'];
                    $_SESSION['user_gender']    = $user['user_gender'];

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
        $this->view->users  = $this->model->User->getUsers();
        $this->view->action = 'removeUser';
        $this->view->setTitle('Supprimer un utilisateur');
        $this->view->setViewName('admin/wUsers');
        $this->view->render();
    }

    public function renderRemoveUser()
    {
        if (!empty($this->params['user_id'])) {
            $this->model->User->deleteUserById($this->params['user_id']);
            $this->view->growler('Utilisateur supprimé.', GROWLER_OK);
        } else {
            $this->view->growlerError();
        }
        $this->render();
    }

    public function renderMail()
    {
        $this->view->setTitle('Message à tous les users');
        $this->view->setViewName('admin/wMail');
        $this->view->render();
    }

    public function renderMailSubmit()
    {
        if (!empty($this->params['mail_content'])) {
            $from    = User::getContextUser('id');
            $content = htmlentities($this->params['mail_content'], ENT_QUOTES, 'utf-8');
            $users   = User::find(array('user_id'), array('!user_id' => User::getContextUser('id')));

            $sentMails = 0;
            foreach ($users as $user) {
                if ($this->model->mail->sendMail($from, $user['user_id'], $content)) {
                    $sentMails++;
                }
            }

            if ($sentMails > 0) {
                $this->view->growler($sentMails.' Emails envoyés.', GROWLER_OK);
            } else {
                $this->view->growlerError();
            }
        } else {
            $this->view->growlerError('Le message vide.');
        }

        $this->render();
    }
}
