<?php

class AdminNewsController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_ADMIN;

    public function render()
    {
        $this->_view->setTitle('Administration');
        $this->_view->setViewName('admin/wAdminNews');
        $this->_view->render();
    }

    public function renderNews()
    {
        $this->_view->news = $this->_model->load('news')->getNews();
        $this->_view->setTitle('News');
        $this->_view->setViewName('admin/wNews');
        $this->_view->render();
    }

    public function renderEdit()
    {
        if (!empty($this->params['value'])) {
            $this->_view->currentNews = $this->_model->load('news')->getNewsById($this->params['value']);
            $this->_view->setTitle('Edition news');
        }
        $this->_view->setTitle('Ajouter une news');
        $this->_view->setViewName('admin/wNewsEdit');
        $this->_view->render();
    }

    public function renderSave()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($this->params['news_titre'])) {
            $this->params['news_titre']     = htmlentities($this->params['news_titre'], ENT_QUOTES, 'utf-8');
            $this->params['news_contenu']   = (!empty($this->params['news_contenu'])) ? htmlentities(str_replace('<br />', '', $this->params['news_contenu']), ENT_QUOTES, 'utf-8') : '';
            $this->params['news_photo_url'] = (!empty($this->params['news_photo_url'])) ? $this->params['news_photo_url'] : '';
            $this->params['news_media_url'] = (!empty($this->params['news_media_url'])) ? $this->params['news_media_url'] : '';
            if (!empty($this->params['news_id'])) {
                if ($this->_model->load('news')->updateNewsById($this->params)) {
                    $this->_view->growler('Modifications enregistrées', GROWLER_OK);
                } else {
                    $this->_view->growlerError();
                    $this->renderEdit();
                }
            } else {
                if ($this->_model->load('news')->addNews($this->params)) {
                    $this->_view->growler('News créée', GROWLER_OK);
                } else {
                    $this->_view->growlerError();
                    $this->renderEdit();
                }
            }
            $this->renderNews();
        }
    }

    public function renderDelete()
    {
        if (!empty($this->params['value'])) {
            $this->_model->load('News')->deleteNewsById($this->params['value']);
            $this->_view->growler('News supprimée', GROWLER_OK);
        } else {
            $this->_view->growlerError();
        }
        $this->renderNews();
    }
}
