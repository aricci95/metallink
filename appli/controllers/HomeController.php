<?php
class HomeController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_NONE;

    public function render()
    {
        $socialAppsData = $this->context->get('userprofile');

        if (!empty($socialAppsData['email']) && $socialAppsData['verified']) {
            if (!$this->get('Facebook')->login()) {
                $this->redirect('subscribe');
            }
        }

        // Découverte
        $tmp = $this->model->news->getNews(3, true);

        $this->view->setViewName('wHome');

        $concertCriterias = array(
            'search_distance' => 10,
        );

        $concerts = $this->model->concert->getSearch($concertCriterias, 0, 5);

        $this->view->concert = !empty($concerts) ? $concerts[min(array_keys($concerts))] : null;

        $this->view->render();
    }

    public function renderLogin()
    {
        if (!empty($this->context->params['user_login']) && !empty($this->context->params['user_pwd'])) {
            $_COOKIE['300gp'] = '';
            $web              = $_COOKIE['300gp'];
            $duration         = 3600;
            $web2             = (!empty($_COOKIE['metallink'])) ? $_COOKIE['metallink'] : '';
            setcookie('300gp', $web, time() + $duration, '/', '.metallink.fr');
            setcookie('metallink', $web2, time() + $duration, '/', '.metallink.fr');
            $login = trim($this->context->params['user_login']);

            // Vérification de password
            try {
                $logResult = $this->get('auth')->checkLogin($login, $this->context->params['user_pwd']);
            } catch (Exception $e) {
                Log::err($e->getMessage());
                $this->redirect('home', array('msg' => $e->getCode()));
            }

            if ($logResult) {
                # On vérifie l'existence du cookie à l'aide de isset, en sachant que le contenu des cookies est contenu dans les variables $_COOKIE
                if (isset($this->context->params['savepwd'])) {
                    if ($this->context->params['savepwd'] == 'on') {
                        # On créer le cookie avec setcookie();
                        setcookie("MlinkLogin", $login, time() + 360000);
                        setcookie("MlinkPwd", $this->context->params['user_pwd'], time() + 360000);
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
