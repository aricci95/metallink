﻿<?php

class ForumController extends AppController
{

    protected $_JS = array(JS_FORUM);

    public function render()
    {
        $this->view->messages      = $this->model->Forum->getLastMessages();
        $this->view->users         = $this->model->Forum->getConnectedUsers();
        $reversedArray             = array_reverse($this->view->messages);
        $this->view->lastId        = !empty($reversedArray) ? $reversedArray[0]['id'] : 0;
        $this->view->notification  = !isset($_SESSION['forum_notification']) ? 1 : $_SESSION['forum_notification'];

        $this->view->setViewName('forum/wForum');
        $this->view->render();
    }

    public function renderFeed()
    {
        $this->view->messages = $this->model->Forum->getLastMessages();
        $this->view->setViewName('forum/wFeed');
        $this->view->render('frameView');
    }

    public function renderUsers()
    {
        $this->view->users = $this->model->Forum->getConnectedUsers();
        $this->view->setViewName('forum/wConnectedUsers');
        $this->view->render('frameView');
    }

    public function renderRefreshFeed()
    {
        $messages = $this->model->Forum->getLastMessages($this->params['id']);
        if (!empty($messages)) {
            $this->view->messages = $messages;
            $this->view->getJSONResponse('forum/wMessages');
        } else {
            echo 404;
        }
    }

    public function renderRefreshUsers()
    {
        $this->view->users = $this->model->Forum->getConnectedUsers();
        $this->view->getJSONResponse('forum/wUsers');
    }

    public function renderSave()
    {
        if (!empty($this->params['content'])) {
            return $this->get('message')->post($this->params['content']);
        } else {
            return false;
        }
    }

    public function renderSetNotification()
    {
        if (User::updateById(User::getContextUser('id'), 'forum_notification', $this->params['notification'])) {
            $_SESSION['forum_notification'] = (int) $this->params['notification'];
            return JSON_OK;
        } else {
            return JSON_ERR;
        }
    }
}
