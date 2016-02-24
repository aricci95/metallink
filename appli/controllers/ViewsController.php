<?php

class ViewsController extends AppController
{

    public function render()
    {
        $this->view->addJS(JS_SCROLL_REFRESH);
        $this->view->elements = $this->model->views->getUserViews();
        $this->view->type = 'user';
        $this->view->setViewName('wViews');
        $this->view->render();
    }

    public function renderMore()
    {
        $offset = $this->context->params['value'];
        $this->view->elements = $this->model->views->getUserViews($offset);
        $this->view->type = 'user';
        $this->view->offset = $offset++;
        $this->view->getJSONResponse('user/wItems');
    }
}
