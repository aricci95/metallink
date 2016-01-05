<?php

class MessageController extends AppController
{

    private function _getDestinataire($parentMessages, $userId)
    {
        if (empty($parentMessages) && $userId > 0) {
            return $this->model->User->getUserByIdDetails($userId);
        }
        // Si rajout de mail de soit même
        if ($parentMessages[0]['expediteur'] == User::getContextUser('id')) {
            return $this->model->User->getUserByIdDetails($parentMessages[0]['destinataire']);
        } // Si réponse
        else {
            return $this->model->User->getUserByIdDetails($parentMessages[0]['expediteur']);
        }

        // Si nouveau message, on récupère les infos du destinataire
        if (isset($this->params['destinataire_id'])) {
            return $this->model->User->getUserByIdDetails($this->params['destinataire_id']);
        } elseif (isset($this->params['user_id'])) {
            return $this->model->User->getUserByIdDetails($this->params['user_id']);
        }
    }

    private function _checkMessages($parentMessages, $destinataireId)
    {
        $contextUserId = User::getContextUser('id');

        // Si nouvelle conversation
        if (empty($parentMessages) || $parentMessages[0]['state_id'] == STATUS_ADMIN) {
            return true;
        }

        if ($parentMessages[0]['destinataire'] != $contextUserId && $parentMessages[0]['expediteur'] != $contextUserId) {
            Log::hack('tentative d\'accès a une conversation étrangère.');
            return false;
        }

        foreach ($parentMessages as $key => $value) {
            $parentMessages[$key]['content'] = Tools::toSmiles($value['content']);
            // Si nouveau mail, alors on le mets en état lu
            if ($value['state_id'] == STATUS_SENT && $value['expediteur'] != $contextUserId) {
                $this->model->message->updateMessageState($value['message_id'], STATUS_READ);
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
        $parentMessages = $this->model->message->getConversation($userId);

        if ($this->_checkMessages($parentMessages, $userId)) {
            $this->view->parentMessages  = $parentMessages;
            $this->view->destinataire = $this->_getDestinataire($parentMessages, $userId);
            $this->view->setViewName('mail/wMain');
            $this->view->render();
        } else {
            $this->redirect('mailbox', array('msg' => ERR_CONTENT));
        }
    }

    public function renderSubmit()
    {
        if (!empty($this->params['last_content']) && !empty($this->params['content']) && $this->params['last_content'] === $this->params['content']) {
            $this->render();
            return;
        }

        if (empty($this->params['destinataire'])) {
            Log::err('destinataire vide');
            $this->redirect('mailbox', array('msg' => ERR_DEFAULT));
        }

        $isLinked = $this->model->Link->isLinked($this->params['destinataire']);

        if (!$isLinked) {
            Log::err('destinataire sans link');
            $this->redirect('mailbox', array('msg' => ERR_DEFAULT));
        }

        $from    = User::getContextUser('id');
        $to      = $this->params['destinataire'];

        $content = str_replace('\\', '', htmlentities($this->params['content'], ENT_QUOTES, 'utf-8'));

        if (empty($content)) {
            $this->view->growler('Message vide.', GROWLER_INFO);
            $this->render();
            return;
        }

        $content = nl2br($content);

        if ($this->get('message')->send($from, $to, $content)) {
            $message = User::getContextUser('login') . ' vous a envoyé un nouveau message ! <a href="http://metallink.fr/mail/' . User::getContextUser('id') . '">Cliquez ici</a> pour le lire.';
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
        $parentMessages = $this->model->message->getConversation($userId, $offset);
        if (count($parentMessages) > 0) {
            $this->_checkMessages($parentMessages, $userId);
            $this->view->parentMessages  = $parentMessages;
            $this->view->destinataire = $this->_getDestinataire($parentMessages, $userId);
            $this->view->offset = $offset++;
            $this->view->getJSONResponse('mail/wItems');
        } else {
            return;
        }
    }
}
