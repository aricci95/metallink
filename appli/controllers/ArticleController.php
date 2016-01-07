<?php

class ArticleController extends AppController
{

    protected $_JS = array(JS_MODAL);

    public function render()
    {
        if (empty($this->params['value'])) {
            $this->redirect('sales', array('msg' => ERR_CONTENT));
        }

        // Récupération des informations de l'utilisateur
        $article = $this->model->Article->getById($this->params['value']);
        if (empty($article['user_login'])) {
            $this->redirect('sales', array('msg' => ERR_SELLER));
        }

        $article['art_description'] = Tools::toSmiles(nl2br($article['art_description']));
        if (empty($article['art_photo_url'])) {
            $article['art_photo_url'] = 'unknowUser.jpg';
        }

        $this->view->article = $article;
        $this->view->link    = $this->model->Link->getLink($article['user_id']);

        // Récupération des photos
        $this->view->photos = $this->model->Photo->getPhotosByKey($this->params['value'], PHOTO_TYPE_ARTICLE);
        $this->view->setViewName('article/wMain');
        $this->view->render();
    }

    public function renderEdit($isValid = true)
    {
        $this->view->categories = $this->model->Article->getCategories();

        if (!empty($this->params['value'])) {
            $article = $this->model->Article->getById($this->params['value']);
            $article['art_libel']       = htmlentities($article['art_libel'], ENT_QUOTES, 'utf-8');
            $article['art_description'] = htmlentities($article['art_description'], ENT_QUOTES, 'utf-8');
            $this->view->article = $article;
            $this->view->setTitle('Edition de l\'article');
        } else {
            $this->view->article = null;
            $this->view->setTitle('Nouvel article');
        }
        if (!$isValid) {
            $this->view->article = $this->params;
        }

        $this->view->setViewName('article/wEdit');
        $this->view->render();
    }

    private function _isValid(array &$values)
    {
        if (empty($values['art_libel'])) {
            $this->view->growler('Le libellé est obligatoire.', GROWLER_ERR);
            return false;
        }
        if (empty($values['categorie_id'])) {
            $this->view->growler('La catégorie est obligatoire.', GROWLER_ERR);
            return false;
        }
        if (!empty($values['art_price'])) {
            $artPrice = str_replace(',', '.', $values['art_price']);
            if (!is_numeric($artPrice)) {
                $this->view->growler('Le prix doit être un nombre avec ou sans virgule.', GROWLER_ERR);
                return false;
            }
        } else {
            $values['art_price'] = 0;
        }
        return true;
    }

    public function renderSave()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->_isValid($this->params)) {
                $this->params['art_libel']       = htmlentities($this->params['art_libel'], ENT_QUOTES, 'utf-8');
                $this->params['art_description'] = htmlentities($this->params['art_description'], ENT_QUOTES, 'utf-8');
                if (!empty($this->params['art_id'])) {
                    if ($this->model->Article->updateArticle($this->params)) {
                        $this->view->growler('Modifications enregistrées.', GROWLER_OK);
                    } else {
                        $this->view->growlerError();
                    }
                    $this->render();
                } else {
                    $articleId = $this->model->Article->createArticle($this->params);
                    if ($articleId > 0) {
                        $this->view->growler('Article enregistré.', GROWLER_OK);
                        $this->params['value'] = $articleId;
                        $this->render();
                    } else {
                        $this->view->growler('Une erreur est survenue, merci de réessayer.', GROWLER_ERR);
                        $this->renderEdit();
                    }
                }
            } else {
                $this->renderEdit(false);
            }
        } else {
            $this->redirect('sales');
        }
    }

    public function renderDelete()
    {
        // Suppression de l'utilisateur
        $this->model->Article->deleteArticleById($this->params['value']);
        $this->view->growler('Article supprimé.');
        // redirection
        $this->redirect('sales', array('msg' => MSG_ARTICLE_DELETE_SUCCESS));
    }
}
