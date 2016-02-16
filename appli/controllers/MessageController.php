<?php

class MessageController extends AppController
{

    private function _getDestinataire($parentMessages, $userId)
    {
        if (empty($parentMessages) && $userId > 0) {
            return $this->model->User->getUserByIdDetails($userId);
        }
        // Si rajout de message de soit même
        if ($parentMessages[0]['expediteur_id'] == User::getContextUser('id')) {
            return $this->model->User->getUserByIdDetails($parentMessages[0]['destinataire_id']);
        } // Si réponse
        else {
            return $this->model->User->getUserByIdDetails($parentMessages[0]['expediteur_id']);
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
        if (empty($parentMessages)) {
            return true;
        }

        if ($parentMessages[0]['destinataire_id'] != $contextUserId && $parentMessages[0]['expediteur_id'] != $contextUserId) {
            Log::hack('tentative d\'accès a une conversation étrangère.');
            return false;
        }

        foreach ($parentMessages as $key => $value) {
            $parentMessages[$key]['content'] = Tools::toSmiles($value['content']);

            // Si nouveau message, alors on le mets en état lu
            if ($value['state_id'] == MESSAGE_STATUS_SENT && $value['expediteur_id'] != $contextUserId) {
                $this->model->message->updateMessageState($value['message_id'], MESSAGE_STATUS_READ);
                if ($_SESSION['new_messages'] > 0) {
                    $_SESSION['new_messages']--;
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
        }

        $userId = $this->params['value'];

        $isLinked = $this->model->Link->isLinked($userId);

        if (!$isLinked) {
            Log::err('destinataire sans link');
            $this->redirect('mailbox', array('msg' => ERR_DEFAULT));
        }

        // On récupère les information du message
        $parentMessages = $this->model->message->getConversation($userId);

        if ($this->_checkMessages($parentMessages, $userId)) {
            $this->view->parentMessages  = $parentMessages;
            $this->view->destinataire = $this->_getDestinataire($parentMessages, $userId);
            $this->view->setViewName('message/wMain');
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

        if (empty($this->params['destinataire_id'])) {
            Log::err('destinataire vide');
            $this->redirect('mailbox', array('msg' => ERR_DEFAULT));
        }

        $isLinked = $this->model->Link->isLinked($this->params['destinataire_id']);

        if (!$isLinked) {
            Log::err('destinataire sans link');
            $this->redirect('mailbox', array('msg' => ERR_DEFAULT));
        }

        $from = User::getContextUser('id');
        $to   = $this->params['destinataire_id'];

        if (empty($this->params['content'])) {
            $this->view->growler('Message vide.', GROWLER_INFO);
            $this->render();
            return;
        }


        if ($this->get('message')->send($from, $to, $this->params['content'])) {
            $message = User::getContextUser('login') . ' vous a envoyé un nouveau message ! <a href="http://metallink.fr/message/' . User::getContextUser('id') . '">Cliquez ici</a> pour le lire.';
            $this->redirect('message', array($this->params['value'], 'msg' => MSG_SENT_OK));
        } else {
            Log::err('impossible d\'enregistrer le message.');
            $this->redirect('mailbox', array('msg' => ERR_MAIL));
        }

        return;
    }

    public function renderMore()
    {
        if (empty($this->params['value']) || empty($this->params['option'])) {
            return;
        }

        $offset = $this->params['value'];
        $userId = $this->params['option'];

        // On récupère les information du message
        $parentMessages = $this->model->message->getConversation($userId, $offset);

        if (count($parentMessages) > 0) {
            $this->_checkMessages($parentMessages, $userId);
            $this->view->parentMessages  = $parentMessages;
            $this->view->destinataire = $this->_getDestinataire($parentMessages, $userId);
            $this->view->offset = $offset++;
            $this->view->getJSONResponse('message/wItems');
        } else {
            return;
        }
    }
}
