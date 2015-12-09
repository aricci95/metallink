<?php

class ForumController extends AppController
{

    protected $_JS = array(JS_FORUM);

    public function render()
    {
        $this->_view->messages       = $this->_model->Forum->getLastMessages();
        $this->_view->users          = $this->_model->Forum->getConnectedUsers();
        $reversedArray               = array_reverse($this->_view->messages);
        $this->_view->lastId         = !empty($reversedArray) ? $reversedArray[0]['id'] : 0;
        $this->_view->setViewName('forum/wForum');
        $this->_view->render();
    }

    public function renderFeed()
    {
        $this->_view->messages = $this->_model->Forum->getLastMessages();
        $this->_view->setViewName('forum/wFeed');
        $this->_view->render('frameView');
    }

    public function renderUsers()
    {
        $this->_view->users = $this->_model->Forum->getConnectedUsers();
        $this->_view->setViewName('forum/wConnectedUsers');
        $this->_view->render('frameView');
    }

    public function renderRefreshFeed()
    {
        $messages = $this->_model->Forum->getLastMessages($this->params['id']);
        if (!empty($messages)) {
            $this->_view->messages = $messages;
            $this->_view->getJSONResponse('forum/wMessages');
        } else {
            return null;
        }
    }

    public function renderRefreshUsers()
    {
        $this->_view->users = $this->_model->Forum->getConnectedUsers();
        $this->_view->getJSONResponse('forum/wUsers');
    }

    public function renderSave()
    {
        if (!empty($this->params['content'])) {
            echo $this->_model->Forum->saveMessage($this->params['content']);
        } else {
            return null;
        }

    }
}
