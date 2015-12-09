<?php

class ViewsController extends AppController
{

    protected $_JS = array(JS_SCROLL_REFRESH);

    public function render()
    {
        $this->_view->elements = $this->_model->Views->getUserViews();
        $this->_view->type = 'user';
        $this->_view->setViewName('wViews');
        $this->_view->render();
    }

    public function renderMore()
    {
        $offset = $this->params['value'];
        $this->_view->elements = $this->_model->Views->getUserViews($offset);
        $this->_view->type = 'user';
        $this->_view->offset = $offset++;
        $this->_view->getJSONResponse('user/wItems');
    }
}
