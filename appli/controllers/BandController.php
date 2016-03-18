<?php

class BandController extends AppController
{

    public function render()
    {
        $this->view->setViewName('band/wMain');
        $this->view->render('frameView');
    }

}