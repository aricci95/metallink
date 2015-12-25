<?php

class MailController extends AppController
{

    private function _getDestinataire($parentMails, $userId)
    {
        if (empty($parentMails) && $userId > 0) {
            return $this->model->User->getUserByIdDetails($userId);
        }
        // Si rajout de mail de soit même
        if ($parentMails[0]['mail_expediteur'] == User::getContextUser('id')) {
            return $this->model->User->getUserByIdDetails($parentMails[0]['mail_destinataire']);
        } // Si réponse
        else {
            return $this->model->User->getUserByIdDetails($parentMails[0]['mail_expediteur']);
        }

        // Si nouveau message, on récupère les infos du destinataire
        if (isset($this->params['destinataire_id'])) {
            return $this->model->User->getUserByIdDetails($this->params['destinataire_id']);
        } elseif (isset($this->params['user_id'])) {
            return $this->model->User->getUserByIdDetails($this->params['user_id']);
        }
    }

    private function _checkMails($parentMails, $destinataireId)
    {
        $contextUserId = User::getContextUser('id');

        // Si nouvelle conversation
        if (empty($parentMails) || $parentMails[0]['mail_state_id'] == MAIL_STATUS_ADMIN) {
            return true;
        }

        if ($parentMails[0]['mail_destinataire'] != $contextUserId && $parentMails[0]['mail_expediteur'] != $contextUserId) {
            Log::hack('tentative d\'accès a une conversation étrangère.');
            return false;
        }

        foreach ($parentMails as $key => $value) {
            $parentMails[$key]['mail_content'] = Tools::toSmiles($value['mail_content']);
            // Si nouveau mail, alors on le mets en état lu
            if ($value['mail_state_id'] == MAIL_STATUS_SENT && $value['mail_expediteur'] != $contextUserId) {
                $this->model->Mail->updateMailState($value['mail_id'], MAIL_STATUS_READ);
                if ($_SESSION['new_mails'] > 0) {
                    $_SESSION['new_mails']--;
                }
            }
        }

        return true;
    }

    public function render()
    {
        $this->view->addJS(JS_SCROLL_REFRESH);
        if (empty($this->params['value'])) {
            $this->view->growler('Message introuvable', GROWLER_ERR);
            $this->redirect('mailbox', array('msg' => ERR_CONTENT));
        } else {
            $isLinked = $this->model->Link->isLinked($this->params['value']);
            if (!$isLinked) {
                Log::err('destinataire sans link');
                $this->redirect('mailbox', array('msg' => ERR_DEFAULT));
            }
            $userId = $this->params['value'];
        }

        // On récupère les information du mail
        $parentMails = $this->model->Mail->getConversation($userId);

        if ($this->_checkMails($parentMails, $userId)) {
            $this->view->parentMails  = $parentMails;
            $this->view->destinataire = $this->_getDestinataire($parentMails, $userId);
            $this->view->setViewName('mail/wMain');
            $this->view->render();
        } else {
            $this->redirect('mailbox', array('msg' => ERR_CONTENT));
        }
    }

    public function renderSubmit()
    {
        if (!empty($this->params['last_content']) && !empty($this->params['mail_content']) && $this->params['last_content'] === $this->params['mail_content']) {
            $this->render();
            return;
        }

        if (empty($this->params['mail_destinataire'])) {
            Log::err('destinataire vide');
            $this->redirect('mailbox', array('msg' => ERR_DEFAULT));
        }

        $isLinked = $this->model->Link->isLinked($this->params['mail_destinataire']);

        if (!$isLinked) {
            Log::err('destinataire sans link');
            $this->redirect('mailbox', array('msg' => ERR_DEFAULT));
        }

        $from    = User::getContextUser('id');
        $to      = $this->params['mail_destinataire'];
        $content = htmlentities($this->params['mail_content'], ENT_QUOTES, 'utf-8');

        if (empty($content)) {
            $this->view->growler('Message vide.', GROWLER_INFO);
            $this->render();
            return;
        }

        $content = nl2br($content);

        if ($this->model->Mail->sendMail($from, $to, $content)) {
            $message = User::getContextUser('login').' vous a envoyé un nouveau message ! <a href="http://metallink.fr/mail/' . User::getContextUser('id') . '">Cliquez ici</a> pour le lire.';
            $this->redirect('mail', array($this->params['value'], 'msg' => MSG_SENT_OK));
        } else {
            Log::err('impossible d\'enregistrer le mail.');
            $this->redirect('mailbox', array('msg' => ERR_MAIL));
        }

        return;
    }

    public function renderMore()
    {
        $offset = $this->params['value'];
        $userId = $this->params['option'];

        // On récupère les information du mail
        $parentMails = $this->model->Mail->getConversation($userId, $offset);
        if (count($parentMails) > 0) {
            $this->_checkMails($parentMails, $userId);
            $this->view->parentMails  = $parentMails;
            $this->view->destinataire = $this->_getDestinataire($parentMails, $userId);
            $this->view->offset = $offset++;
            $this->view->getJSONResponse('mail/wItems');
        } else {
            return;
        }
    }
}
