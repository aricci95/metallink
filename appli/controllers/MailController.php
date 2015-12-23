<?php

class MailController extends AppController
{

    private function _getDestinataire($parentMails, $userId)
    {
        if (empty($parentMails) && $userId > 0) {
            return $this->_model->User->getUserByIdDetails($userId);
        }
        // Si rajout de mail de soit même
        if ($parentMails[0]['mail_expediteur'] == User::getContextUser('id')) {
            return $this->_model->User->getUserByIdDetails($parentMails[0]['mail_destinataire']);
        } // Si réponse
        else {
            return $this->_model->User->getUserByIdDetails($parentMails[0]['mail_expediteur']);
        }

        // Si nouveau message, on récupère les infos du destinataire
        if (isset($this->params['destinataire_id'])) {
            return $this->_model->User->getUserByIdDetails($this->params['destinataire_id']);
        } elseif (isset($this->params['user_id'])) {
            return $this->_model->User->getUserByIdDetails($this->params['user_id']);
        }
    }

    private function _checkMails($parentMails, $destinataireId)
    {
        $contextUserId = User::getContextUser('id');

        // Si nouvelle conversation
        if (empty($parentMails)) {
            return true;
        }

        if ($parentMails[0]['mail_destinataire'] != $contextUserId && $parentMails[0]['mail_expediteur'] != $contextUserId) {
            $this->log->hack('tentative d\'accès a une conversation étrangère.');
            return false;
        }

        foreach ($parentMails as $key => $value) {
            $parentMails[$key]['mail_content'] = Tools::toSmiles($value['mail_content']);
            // Si nouveau mail, alors on le mets en état lu
            if ($value['mail_state_id'] == MAIL_STATUS_SENT && $value['mail_expediteur'] != $contextUserId) {
                $this->_model->Mail->updateMailState($value['mail_id'], MAIL_STATUS_READ);
                if ($_SESSION['new_mails'] > 0) {
                    $_SESSION['new_mails']--;
                }
            }
        }

        return true;
    }

    public function render()
    {
        $this->_view->addJS(JS_SCROLL_REFRESH);
        if (empty($this->params['value'])) {
            $this->_view->growler('Message introuvable', GROWLER_ERR);
            $this->redirect('mailbox', array('msg' => ERR_CONTENT));
        } else {
            $isLinked = $this->_model->Link->isLinked($this->params['value']);
            if (!$isLinked) {
                $this->log->err('destinataire sans link');
                $this->redirect('mailbox', array('msg' => ERR_DEFAULT));
            }
            $userId = $this->params['value'];
        }

        // On récupère les information du mail
        $parentMails = $this->_model->Mail->getConversation($userId);
        if ($this->_checkMails($parentMails, $userId)) {
            $this->_view->parentMails  = $parentMails;
            $this->_view->destinataire = $this->_getDestinataire($parentMails, $userId);
            $this->_view->setViewName('mail/wMain');
            $this->_view->render();
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
            $this->log->err('destinataire vide');
            $this->redirect('mailbox', array('msg' => ERR_DEFAULT));
        }

        $isLinked = $this->_model->Link->isLinked($this->params['mail_destinataire']);

        if (!$isLinked) {
            $this->log->err('destinataire sans link');
            $this->redirect('mailbox', array('msg' => ERR_DEFAULT));
        }

        $items['mail_id'] = '';
        $items['mail_expediteur']   = User::getContextUser('id');
        $items['mail_destinataire'] = $this->params['mail_destinataire'];
        $content = htmlentities($this->params['mail_content'], ENT_QUOTES, 'utf-8');

        if (empty($content)) {
            $this->_view->growler('Message vide.', GROWLER_INFO);
            $this->render();
            return;
        }

        $items['mail_content']  = nl2br($content);
        $items['mail_date']     = date("Y-m-d H:i:s");
        $items['mail_state_id'] = MAIL_STATUS_SENT;
        $items['mailbox_id']    = MAIL_STATUS_SENT;

        if ($this->_model->Mail->sendMail($items) != false) {
            $destinataire = $this->_model->User->getMailByUser($this->params['mail_destinataire']);
            $message = User::getContextUser('login').' vous a envoyé un nouveau message ! <a href="http://metallink.fr/mail/' . User::getContextUser('id') . '">Cliquez ici</a> pour le lire.';

            if ($this->_model->mailer->send($destinataire['user_mail'], 'Nouveau message sur MetalLink !', $message)) {
                $this->params['value'] = $items['mail_destinataire'];
                $this->redirect('mail', array($this->params['value'], 'msg' => MSG_SENT_OK));
                return;
            } else {
                $this->log->err('impossible d\'envoyer la notification mail du message.');
                $this->redirect('mailbox', array('msg' => ERR_MAIL));
            }
        } else {
            $this->log->err('impossible d\'enregistrer le mail.');
            $this->redirect('mailbox', array('msg' => ERR_MAIL));
        }
    }

    public function renderMore()
    {
        $offset = $this->params['value'];
        $userId = $this->params['option'];

        // On récupère les information du mail
        $parentMails = $this->_model->Mail->getConversation($userId, $offset);
        if (count($parentMails) > 0) {
            $this->_checkMails($parentMails, $userId);
            $this->_view->parentMails  = $parentMails;
            $this->_view->destinataire = $this->_getDestinataire($parentMails, $userId);
            $this->_view->offset = $offset++;
            $this->_view->getJSONResponse('mail/wItems');
        } else {
            return;
        }
    }
}
