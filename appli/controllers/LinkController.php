<?php

class LinkController extends AppController
{

    protected $_JS = array(JS_SCROLL_REFRESH);

    public function render()
    {
        if (empty($this->params['value'])) {
            $this->view->growlerError();
            $this->redirect('home');
        }

        $status = $this->params['value'];

        if ($status == LINK_STATUS_SENT) {
            $this->view->users['recieved'] = $this->model->Link->getLinksUserByStatus(LINK_STATUS_RECIEVED);
            $this->view->users['sent']     = $this->model->Link->getLinksUserByStatus(LINK_STATUS_SENT);
        } else {
            $this->view->users = $this->model->Link->getLinksUserByStatus($status);
        }

        $this->view->status = $status;
        $this->view->setViewName('link/wList');
        $this->view->render();
    }

    public function renderMore()
    {
        $offset = $this->params['value'];
        $status = $this->params['option'];

        // Récupèration des links & demandes
        if ($status == LINK_STATUS_SENT) {
            $this->view->elements['recieved'] = $this->model->Link->getLinksUserByStatus(LINK_STATUS_RECIEVED, $offset);
            $this->view->elements['sent'] = $this->model->Link->getLinksUserByStatus(LINK_STATUS_SENT, $offset);
        } else {
            $this->view->elements = $this->model->Link->getLinksUserByStatus($status, $offset);
        }

        $this->view->type     = 'user';
        $this->view->offset   = $offset++;
        $this->view->getJSONResponse('user/wItems');
    }

    public function renderLink()
    {
        $destinataireId = $this->params['destinataire_id'];
        $destinataire   = array('user_id'        => $destinataireId,
                                'user_photo_url' => $this->params['destinataire_photo_url'],
                                'user_mail'      => $this->params['destinataire_mail'],
                                'user_login'     => $this->params['destinataire_login']);

        $this->view->user = $destinataire;
        $status = Link::getStatus($destinataireId);
        $result = ($status == LINK_STATUS_NONE) ? $this->model->Link->linkTo($destinataire) : $this->model->Link->updateLink($destinataireId, $this->params['status']);

        if ($result) {
            $this->view->newStatus = $this->params['status'];
            $this->view->getJSONResponse('link/wItem');
        } else {
            echo 500;
        }
    }
}
