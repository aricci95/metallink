<?php

class MailboxController extends AppController
{

    public function render()
    {
        $this->view->addJS(JS_SCROLL_REFRESH);
        $libel = 'De';
        //on récupère les messages de l'utilisateur
        $this->view->userMessages = $this->model->message->getList();

        foreach ($this->view->userMessages as $key => $value) {
            $this->view->userMessages[$key]['content'] = Tools::toSmiles($value['content']);
        }
        $this->view->setViewName('mailbox/wList');
        $this->view->setTitle('Messages reçus');
        $this->view->render();
    }

    public function renderMore($offset = 0)
    {
        $offset = $this->context->params['value'];
        $libel = 'De';

        //on récupère les messages de l'utilisateur
        $this->view->userMessages = $this->model->message->getList($offset);
        if (count($this->view->userMessages) > 0) {
            foreach ($this->view->userMessages as $key => $value) {
                $this->view->userMessages[$key]['content'] = Tools::toSmiles($value['content']);
            }

            $this->view->offset = $offset++;
            $this->view->getJSONResponse('mailbox/wItems');
        } else {
            return;
        }
    }

    public function renderDelete()
    {
        if (!empty($this->context->params['value'])) {
            $this->model->mailbox->deleteConversation($this->context->params['value']);
            $this->view->growler('Conversation supprimée.', GROWLER_OK);
        }
        $this->render();
    }
}
