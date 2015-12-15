<?php
class HomeController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_NONE;

    public function render()
    {
        // Découverte
        $tmp = $this->_model->news->getNews(3, true);

        $this->_view->setViewName('wHome');

        $this->_view->newUsers    = $this->_model->User->getNew();
        $this->_view->newArticles = $this->_model->article->getNew();
        $this->_view->newCovoits  = $this->_model->covoit->getNew();
        $this->_view->lesNews     = $this->_model->news->getNews(1);
        $this->_view->decouverte  = (isset($tmp[0])) ? $tmp[0] : null;
        $this->_view->render();
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
                $logResult = $this->_model->Auth->checkLogin($login, $this->params['user_pwd']);
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
        //Destruction du Cookie
        setcookie("MlinkPwd", 0);
        setcookie("MlinkLogin", 0);
        session_destroy();
        $this->redirect('home');
    }
}
