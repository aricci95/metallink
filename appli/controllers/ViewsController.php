<?php

class ViewsController extends AppController
{

    protected $_JS = array(JS_SCROLL_REFRESH);

    public function render()
    {
        $this->view->elements = $this->model->Views->getUserViews();
        $this->view->type = 'user';
        $this->view->setViewName('wViews');
        $this->view->render();
    }

    public function renderMore()
    {
        $offset = $this->params['value'];
        $this->view->elements = $this->model->Views->getUserViews($offset);
        $this->view->type = 'user';
        $this->view->offset = $offset++;
        $this->view->getJSONResponse('user/wItems');
    }
}
