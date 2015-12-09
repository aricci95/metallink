<?php

class CronController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_NONE;

    public function renderUserStatuses()
    {
        if ($this->_model->User->refreshUserStatuses()) {
            $executionStatus = 'OK';
        } else {
            $executionStatus = 'ERROR';
        }
        echo $executionStatus;
    }
}
