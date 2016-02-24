<?php

class NewsController extends AppController
{

    public function render()
    {
       // Récupèration de la news concernée
        $currentNews = $this->model->News->getNewsById($this->context->params['value']);
        $lesNews = $this->model->News->getNews();
        $this->view->currentNews  = $currentNews;
        $this->view->newsAuteur   = $this->model->User->getUserByIdDetails($currentNews['news_auteur_id']);
        $this->view->setViewName('wNews');
        $this->view->render();
    }
}
