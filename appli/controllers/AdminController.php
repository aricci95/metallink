<?php

class AdminController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_SUPERADMIN;

    public function render()
    {
        $this->_view->setTitle('Administration');
        $this->_view->setViewName('admin/wAdmin');
        $this->_view->render();
    }

    public function renderSwitch()
    {
        $this->_view->users  = $this->_model->User->getUsers();
        $this->_view->action = 'setSwitch';
        $this->_view->setTitle('User switch');
        $this->_view->setViewName('admin/wUsers');
        $this->_view->render();
    }

    public function renderSetSwitch()
    {
        if (!empty($this->params['user_id'])) {
            $user = $this->_model->User->getById($this->params['user_id']);
            if (!empty($user)) {
                if ($user['user_valid'] != 1) {
                    $this->_view->growler('Utilistateur non validé.', GROWLER_ERR);
                } else {
                    $_SESSION['user_id']        = $user['user_id'];
                    $_SESSION['user_login']     = $user['user_login'];
                    $_SESSION['role_id']        = $user['role_id'];
                    $_SESSION['user_photo_url'] = $user['user_photo_url'];
                    $_SESSION['age']            = $user['age'];
                    $_SESSION['user_valid']     = $user['user_valid'];
                    $_SESSION['user_mail']      = $user['user_mail'];
                    $_SESSION['user_gender']    = $user['user_gender'];

                    $this->redirect('home', array('msg' => MSG_ADM_SWITCH));
                }
            }
        } else {
            $this->_view->growlerError();
        }
        $this->render();
    }

    public function renderDeleteUser()
    {
        $this->_view->users  = $this->_model->User->getUsers();
        $this->_view->action = 'removeUser';
        $this->_view->setTitle('Supprimer un utilisateur');
        $this->_view->setViewName('admin/wUsers');
        $this->_view->render();
    }

    public function renderRemoveUser()
    {
        if (!empty($this->params['user_id'])) {
            $this->_model->User->deleteUserById($this->params['user_id']);
            $this->_view->growler('Utilisateur supprimé.', GROWLER_OK);
        } else {
            $this->_view->growlerError();
        }
        $this->render();
    }

    public function renderMail()
    {
        $this->_view->setTitle('Message à tous les users');
        $this->_view->setViewName('admin/wMail');
        $this->_view->render();
    }

    public function renderMailSubmit()
    {
        if (!empty($this->params['mail_content'])) {
            $mail['mail_expediteur'] = $mail['mail_destinataire'] = User::getContextUser('id');
            $mail['mail_content']    = htmlentities($this->params['mail_content'], ENT_QUOTES, 'utf-8');
            $step1 = $this->_model->load('mail')->sendMail($mail, MAIL_STATUS_ADMIN);
            if ($step1) {
                $sentMails = 0;
                $users     = $this->_model->User->getUsers();
                foreach ($users as $user) {
                    if ($this->_model->load('mailer')->send($user['user_mail'], 'Nouveau message sur MetalLink !', 'Vous avez reçu un nouveau message !')) {
                        $sentMails++;
                    }
                }
            }
            if ($step1 && $sentMails > 0) {
                $this->_view->growler($sentMails.' Emails envoyé.', GROWLER_OK);
            } else {
                $this->_view->growlerError();
            }
        } else {
            $this->_view->growlerError('Le message vide.');
        }
        $this->render();
    }
}
