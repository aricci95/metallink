<?php

class MailboxController extends AppController
{

    public function render()
    {
        $this->_view->addJS(JS_SCROLL_REFRESH);
        $libel = 'De';
        //on récupère les mails de l'utilisateur
        $this->_view->userMails = $this->_model->Mailbox->getInboxMail();
        foreach ($this->_view->userMails as $key => $value) {
            $this->_view->userMails[$key]['mail_content'] = Tools::toSmiles($value['mail_content']);
        }
        $this->_view->setViewName('mailbox/wList');
        $this->_view->setTitle('Messages reçus');
        $this->_view->render();
    }

    public function renderMore($offset = 0)
    {
        $offset = $this->params['value'];
        $libel = 'De';

        //on récupère les mails de l'utilisateur
        $this->_view->userMails = $this->_model->Mailbox->getInboxMail($offset);
        if (count($this->_view->userMails) > 0) {
            foreach ($this->_view->userMails as $key => $value) {
                $this->_view->userMails[$key]['mail_content'] = Tools::toSmiles($value['mail_content']);
            }

            $this->_view->offset = $offset++;
            $this->_view->getJSONResponse('mailbox/wItems');
        } else {
            return;
        }
    }

    public function renderDelete()
    {
        if (!empty($this->params['value'])) {
            $this->_model->mailbox->deleteConversation($this->params['value']);
            $this->_view->growler('Conversation supprimée.', GROWLER_OK);
        }
        $this->render();
    }
}
