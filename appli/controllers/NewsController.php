<?php

class NewsController extends AppController
{

    public function render()
    {
       // Récupèration de la news concernée
        $currentNews = $this->_model->News->getNewsById($this->params['value']);
        $lesNews = $this->_model->News->getNews();
        $this->_view->currentNews  = $currentNews;
        $this->_view->newsAuteur   = $this->_model->User->getUserByIdDetails($currentNews['news_auteur_id']);
        $this->_view->setViewName('wNews');
        $this->_view->render();
    }
}
