<?php
abstract class AppController extends Controller
{

    protected $_authLevel = AUTH_LEVEL_USER;

    public function __construct()
    {
        parent::__construct();

        $this->_checkSession();
        if (!$this->isAjax()) {
            if (!empty($_SESSION['role_id']) && $_SESSION['role_id'] >= AUTH_LEVEL_USER) {
                try {
                    $this->_getNotifications();
                    $this->_refreshLastConnexion();
                } catch (Exception $e) {
                    $this->view->growlerError();
                    Mailer::sendError($e);
                }
            }
        }
    }

    private function _getNotifications()
    {
        // vues
        $_SESSION['views'] = $this->model->Auth->countViews(User::getContextUser('id'));

        // Récupération & comptage des links
        $oldLinks = (!empty($_SESSION['links'])) ? $_SESSION['links'] : null;
        $newLinks = $this->model->Link->setContextUserLinks();

        if (!empty($oldLinks) && $oldLinks['count'][LINK_STATUS_RECIEVED] < $newLinks['count'][LINK_STATUS_RECIEVED]) {
            $this->view->growler('Nouvelle demande !', GROWLER_INFO);
        }

        // Vérification des nouveaux messages
        $oldMessagesCount  = (!empty($_SESSION['new_messages'])) ? $_SESSION['new_messages'] : 0;
        $_SESSION['new_messages'] = $this->model->Auth->countNewMessages(User::getContextUser('id'));

        if ($oldMessagesCount < $_SESSION['new_messages']) {
            $this->view->growler('Nouveau message !', GROWLER_INFO);
        }

        if (!isset($_SESSION['forum_notification']) || $_SESSION['forum_notification'] == 1) {
            // Vérification dernier message forum
            $lastMessage = Forum::getLastMessage();

            if (!empty($lastMessage)) {
                if (empty($_SESSION['last_forum_message'])) {
                    $this->_forumGrowler($lastMessage);
                } else {
                    if ($lastMessage['id'] != $_SESSION['last_forum_message']['id'] && $lastMessage['date'] != $_SESSION['last_forum_message']['date']) {
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
        $_SESSION['last_forum_message'] = $lastMessage;
    }

    protected function _refreshLastConnexion()
    {
        // Status
        if (!empty($_SESSION['user_id'])) {
            if (!empty($_SESSION['user_last_connexion'])) {
                $now      = time();
                $left     = $_SESSION['user_last_connexion'];
                $timeLeft = $now - $left;
                if ($timeLeft == 0 || $timeLeft > (ONLINE_TIME_LIMIT - 300)) {
                    $this->model->User->updateLastConnexion(User::getContextUser('id'));
                }
            } else {
                $this->model->User->updateLastConnexion(User::getContextUser('id'));
            }
        }
    }

    // Vérifie la conformité de la session et les messages
    protected function _checkSession()
    {
        $roleLimit = $this->_authLevel;

        // Cas user en session
        if (!empty($_SESSION['user_valid']) && !empty($_SESSION['user_id']) && !empty($_SESSION['user_login'])) {
            if ($_SESSION['user_valid'] == 1) {
                if ($_SESSION['role_id'] >= $roleLimit) {
                    return true;
                } else {
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
                $logResult = $this->model->Auth->checkLogin($_COOKIE['MlinkLogin'], $_COOKIE['MlinkPwd']);
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
