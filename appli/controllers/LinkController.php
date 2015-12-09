<?php

class LinkController extends AppController
{

    protected $_JS = array(JS_SCROLL_REFRESH);

    public function render()
    {
        if (empty($this->params['value'])) {
            $this->_view->growlerError();
            $this->redirect('home');
        }
        $status = $this->params['value'];
        if ($status == LINK_STATUS_SENT) {
            $this->_view->users['recieved'] = $this->_model->Link->getLinksUserByStatus(LINK_STATUS_RECIEVED);
            $this->_view->users['sent']     = $this->_model->Link->getLinksUserByStatus(LINK_STATUS_SENT);
        } else {
            $this->_view->users = $this->_model->Link->getLinksUserByStatus($status);
        }
        $this->_view->status = $status;
        $this->_view->setViewName('link/wList');
        $this->_view->render();
    }

    public function renderMore()
    {
        $offset = $this->params['value'];
        $status = $this->params['option'];
        // Récupèration des links & demandes
        if ($status == LINK_STATUS_SENT) {
            $this->_view->elements['recieved'] = $this->_model->Link->getLinksUserByStatus(LINK_STATUS_RECIEVED, $offset);
            $this->_view->elements['sent'] = $this->_model->Link->getLinksUserByStatus(LINK_STATUS_SENT, $offset);
        } else {
            $this->_view->elements = $this->_model->Link->getLinksUserByStatus($status, $offset);
        }
        $this->_view->type     = 'user';
        $this->_view->offset   = $offset++;
        $this->_view->getJSONResponse('user/wItems');
    }

    public function renderLink()
    {
        $destinataireId = $this->params['destinataire_id'];
        $destinataire   = array('user_id'        => $destinataireId,
                                'user_photo_url' => $this->params['destinataire_photo_url'],
                                'user_mail'      => $this->params['destinataire_mail'],
                                'user_login'     => $this->params['destinataire_login']);
        $this->_view->user = $destinataire;
        $status = $this->getLinkStatus($destinataireId);
        $result = ($status == LINK_STATUS_NONE) ? $this->_model->Link->linkTo($destinataire) : $this->_model->Link->updateLink($destinataireId, $this->params['status']);
        if ($result) {
            $this->_view->newStatus = $this->params['status'];
            $this->_view->getJSONResponse('link/wItem');
        } else {
            echo 500;
        }
    }
}
