<?php

class MailboxController extends AppController
{

    public function render()
    {
        $this->view->addJS(JS_SCROLL_REFRESH);
        $libel = 'De';
        //on récupère les mails de l'utilisateur
        $this->view->userMails = $this->model->Mailbox->getInboxMail();
        foreach ($this->view->userMails as $key => $value) {
            $this->view->userMails[$key]['mail_content'] = Tools::toSmiles($value['mail_content']);
        }
        $this->view->setViewName('mailbox/wList');
        $this->view->setTitle('Messages reçus');
        $this->view->render();
    }

    public function renderMore($offset = 0)
    {
        $offset = $this->params['value'];
        $libel = 'De';

        //on récupère les mails de l'utilisateur
        $this->view->userMails = $this->model->Mailbox->getInboxMail($offset);
        if (count($this->view->userMails) > 0) {
            foreach ($this->view->userMails as $key => $value) {
                $this->view->userMails[$key]['mail_content'] = Tools::toSmiles($value['mail_content']);
            }

            $this->view->offset = $offset++;
            $this->view->getJSONResponse('mailbox/wItems');
        } else {
            return;
        }
    }

    public function renderDelete()
    {
        if (!empty($this->params['value'])) {
            $this->model->mailbox->deleteConversation($this->params['value']);
            $this->view->growler('Conversation supprimée.', GROWLER_OK);
        }
        $this->render();
    }
}
