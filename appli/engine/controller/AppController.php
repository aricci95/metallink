<?php
abstract class AppController extends Controller
{

    protected $_authLevel = AUTH_LEVEL_USER;

    public function __construct()
    {
        parent::__construct();
        $this->_checkSession();

        if (!$this->isAjax()) {
            $role_id = $this->context->get('role_id');

            if ($role_id >= AUTH_LEVEL_USER) {
                try {
                    $this->_getNotifications();
                    $this->_refreshLastConnexion();
                } catch (Exception $e) {
                    $this->view->growlerError();

                    $this->get('mailer')->sendError($e);
                }
            }
        }
    }

    private function _getNotifications()
    {
        // vues
        $viewCount = $this->model->Auth->countViews($this->context->get('user_id'));

        $this->context->set('views', (int) $viewCount);

        // Récupération & comptage des links
        $oldLinks = $this->context->get('links');
        $newLinks = $this->get('link')->setContextUserLinks();

        if (!empty($oldLinks) && $oldLinks['count'][LINK_STATUS_RECEIVED] < $newLinks['count'][LINK_STATUS_RECEIVED]) {
            $this->view->growler('Nouvelle demande !', GROWLER_INFO);
        }

        // Vérification des nouveaux messages
        $oldMessagesCount  = $this->context->get('new_messages');

        $this->context->set('new_messages', $this->model->Auth->countNewMessages($this->context->get('user_id')));

        if ($oldMessagesCount < $this->context->get('new_messages')) {
            $this->view->growler('Nouveau message !', GROWLER_INFO);
        }

        if ($this->context->get('forum_notification')) {
            // Vérification dernier message forum
            $lastMessage = $this->model->forum->getLastMessage();

            if (!empty($lastMessage)) {
                if (!$this->context->get('last_forum_message_id')) {
                    $this->_forumGrowler($lastMessage);
                } else {
                    if ($lastMessage['id'] != $this->context->get('last_forum_message_id') && $lastMessage['date'] != $this->context->get('last_forum_message_date')) {
                        $this->_forumGrowler($lastMessage);
                    }
                }
            }
        }
    }

    private function _forumGrowler($lastMessage) {
        $title = $lastMessage['user_login'] . ' (forum)';
        $this->view->growler($lastMessage['content'] , GROWLER_INFO, $title);

        unset($lastMessage['content']);

        $this->context->set('last_forum_message_id', $lastMessage['id'])
                      ->set('last_forum_message_date', $lastMessage['date']);
    }

    protected function _refreshLastConnexion()
    {
        // Status
        if ($this->context->get('user_id')) {
            if ($this->context->get('user_last_connexion')) {
                $now      = time();
                $left     = $this->context->get('user_last_connexion');
                $timeLeft = $now - $left;

                if ($timeLeft == 0 || $timeLeft > (ONLINE_TIME_LIMIT - 300)) {
                    $this->model->User->updateLastConnexion($this->context->get('user_id'));
                }
            } else {
                $this->model->User->updateLastConnexion($this->context->get('user_id'));
            }
        }
    }

    // Vérifie la conformité de la session
    protected function _checkSession()
    {
        $roleLimit = $this->_authLevel;

        // Cas user en session
        if ($this->context->get('user_valid') && $this->context->get('user_id') && $this->context->get('user_login')) {
            if ($this->context->get('user_valid') == 1) {
                if ($this->context->get('role_id') >= $roleLimit) {
                    return true;
                } else {
                    die;
                    // Utilisateur valide mais droits insuffisants
                    $this->redirect('home', array('msg' => ERR_AUTH));
                }
            } else {
                // Message non validé
                session_destroy();
                $this->redirect('home', array('msg' => ERR_NOT_VALIDATED));
            }
        } // Cas pas d'user en session, vérification des cookies
        elseif (!empty($_COOKIE['MlinkLogin']) && !empty($_COOKIE['MlinkPwd'])) {
            try {
                $logResult = $this->get('auth')->checkLogin($_COOKIE['MlinkLogin'], $_COOKIE['MlinkPwd']);
            } catch (Exception $e) {
                $this->redirect('home', array('msg' => $e->getCode()));
            }

            return $logResult;
        } // Cas page accès sans autorisation
        elseif ($roleLimit != AUTH_LEVEL_NONE) {
            $this->redirect('home', array('msg' => ERR_AUTH));
        }

        return false;
    }
}
