<?php

class AdminNewsController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_ADMIN;

    public function render()
    {
        $this->view->setTitle('Administration');
        $this->view->setViewName('admin/wAdminNews');
        $this->view->render();
    }

    public function renderNews()
    {
        $this->view->news = $this->model->load('news')->getNews();
        $this->view->setTitle('News');
        $this->view->setViewName('admin/wNews');
        $this->view->render();
    }

    public function renderEdit()
    {
        if (!empty($this->params['value'])) {
            $this->view->currentNews = $this->model->load('news')->getNewsById($this->params['value']);
            $this->view->setTitle('Edition news');
        }
        $this->view->setTitle('Ajouter une news');
        $this->view->setViewName('admin/wNewsEdit');
        $this->view->render();
    }

    public function renderSave()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($this->params['news_titre'])) {
            $this->params['news_titre']     = htmlspecialchars($this->params['news_titre'], ENT_QUOTES, 'utf-8');
            $this->params['news_contenu']   = (!empty($this->params['news_contenu'])) ? htmlspecialchars(str_replace('<br />', '', $this->params['news_contenu']), ENT_QUOTES, 'utf-8') : '';
            $this->params['news_photo_url'] = (!empty($this->params['news_photo_url'])) ? $this->params['news_photo_url'] : '';
            $this->params['news_media_url'] = (!empty($this->params['news_media_url'])) ? $this->params['news_media_url'] : '';
            if (!empty($this->params['news_id'])) {
                if ($this->model->load('news')->updateNewsById($this->params)) {
                    $this->view->growler('Modifications enregistrées', GROWLER_OK);
                } else {
                    $this->view->growlerError();
                    $this->renderEdit();
                }
            } else {
                if ($this->model->load('news')->addNews($this->params)) {
                    $this->view->growler('News créée', GROWLER_OK);
                } else {
                    $this->view->growlerError();
                    $this->renderEdit();
                }
            }
            $this->renderNews();
        }
    }

    public function renderDelete()
    {
        if (!empty($this->params['value'])) {
            $this->model->load('News')->deleteNewsById($this->params['value']);
            $this->view->growler('News supprimée', GROWLER_OK);
        } else {
            $this->view->growlerError();
        }
        $this->renderNews();
    }
}
