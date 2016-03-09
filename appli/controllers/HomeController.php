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
            try {
                $authentResult = $this->get('auth')->login($this->context->params['user_login'], $this->context->params['user_pwd']);

                if ($authentResult) {
                    $this->redirect('home');
                }
            } catch (Exception $e) {
                Log::err($e->getMessage());
                $this->redirect('home', array('msg' => $e->getCode()));
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
