<?php

class BandController extends AppController
{

    public function render()
    {
        $band_id = $this->context->getParam('value');

        $band = $this->model->band->findOne(array(), array('band_id' => $band_id));

        if (empty($band['band_logo_url'])) {
            $band = $this->get('band')->fetch($band);
        }

        if (!empty($band)) {
            $this->view->band = $band;
        } else {
            $this->view->growler('Données du groupe introuvables.');
        }

        $this->view->setViewName('band/wMain');
        $this->view->render('frameView');
    }

}