<?php

class BandController extends AppController
{

    public function render()
    {
        $band_id = $this->context->getParam('value');

        $this->view->band = $this->model->band->findOne(array(), array('band_id' => $band_id));

        $this->view->setViewName('band/wMain');
        $this->view->render('frameView');
    }

}