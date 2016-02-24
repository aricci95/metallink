<?php
class HomeController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_NONE;

    public function render()
    {
        if ($this->context->get('userprofile')) {
            $this->get('Facebook')->login();
        }

        // Découverte
        $tmp = $this->model->news->getNews(3, true);

        $this->view->setViewName('wHome');

        $this->view->newUsers    = $this->model->User->getNew();
        $this->view->newArticles = $this->model->article->getNew();
        $this->view->newCovoits  = $this->model->covoit->getNew();
        $this->view->lesNews     = $this->model->news->getNews(1);
        $this->view->decouverte  = (isset($tmp[0])) ? $tmp[0] : null;
        $this->view->render();
    }

    public function renderLogin()
    {
        if (!empty($this->params['user_login']) && !empty($this->params['user_pwd'])) {
            $_COOKIE['300gp'] = '';
            $web              = $_COOKIE['300gp'];
            $duration         = 3600;
            $web2             = (!empty($_COOKIE['metallink'])) ? $_COOKIE['metallink'] : '';
            setcookie('300gp', $web, time() + $duration, '/', '.metallink.fr');
            setcookie('metallink', $web2, time() + $duration, '/', '.metallink.fr');
            $login = trim($this->params['user_login']);

            // Vérification de password
            try {
                $logResult = $this->get('auth')->checkLogin($login, $this->params['user_pwd']);
            } catch (Exception $e) {
                $this->redirect('home', array('msg' => $e->getCode()));
            }

            if ($logResult) {
                # On vérifie l'existence du cookie à l'aide de isset, en sachant que le contenu des cookies est contenu dans les variables $_COOKIE
                if (isset($this->params['savepwd'])) {
                    if ($this->params['savepwd'] == 'on') {
                        # On créer le cookie avec setcookie();
                        setcookie("MlinkLogin", $login, time() + 360000);
                        setcookie("MlinkPwd", $this->params['user_pwd'], time() + 360000);
                    } else {
                        setcookie("MlinkCookie", 0);
                        setcookie("MlinkPwd", 0);
                    }
                }

                $this->redirect('home');
            }
        }

        $this->redirect('home', array('msg' => ERR_LOGIN));
    }

    public function renderDisconnect()
    {
        if ($this->get('auth')->disconnect()) {
            $this->redirect('home');
        } else {
            $this->view->growlerError();
        }
    }
}
