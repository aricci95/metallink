<?php

class LinkController extends AppController
{

    protected $_JS = array(JS_SCROLL_REFRESH);

    public function render()
    {
        if (empty($this->context->params['value'])) {
            $this->view->growlerError();
            $this->redirect('user');
        }

        $status = $this->context->params['value'];

        if ($status == LINK_STATUS_SENT) {
            $this->view->sent = $this->model->Link->getLinksUserByStatus(LINK_STATUS_SENT);
            $this->view->received = $this->model->Link->getLinksUserByStatus(LINK_STATUS_RECEIVED);
        } else {
            $this->view->elements = $this->model->Link->getLinksUserByStatus($status);
        }

        $this->view->status = $status;
        $this->view->setViewName('link/wList');
        $this->view->render();
    }

    public function renderMore()
    {
        $offset = $this->context->params['value'];
        $status = $this->context->params['option'];

        // Récupèration des links & demandes
        if ($status == LINK_STATUS_SENT) {
            $this->view->elements = $this->model->Link->getLinksUserByStatus(LINK_STATUS_SENT, $offset);
        } else {
            $this->view->elements = $this->model->Link->getLinksUserByStatus($status, $offset);
        }

        $this->view->type     = 'user';
        $this->view->offset   = $offset++;
        $this->view->getJSONResponse('user/wItems');
    }

    public function renderLink()
    {
        $destinataireId = $this->context->params['destinataire_id'];
        $destinataire   = array('user_id'        => $destinataireId,
                                'user_photo_url' => $this->context->params['destinataire_photo_url'],
                                'user_mail'      => $this->context->params['destinataire_mail'],
                                'user_login'     => $this->context->params['destinataire_login']);

        $this->view->user = $destinataire;
        $status = $this->get('link')->getLinkStatus($destinataireId);
        $result = ($status == LINK_STATUS_NONE) ? $this->get('link')->linkTo($destinataire) : $this->model->Link->updateLink($destinataireId, $this->context->params['status']);

        if ($result) {
            $this->view->newStatus = $this->context->params['status'];
            $this->view->getJSONResponse('link/wItem');
        } else {
            echo 500;
        }
    }
}
