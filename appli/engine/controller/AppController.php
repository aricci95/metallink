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
                $this->_getNotifications();
                $this->_refreshLastConnexion();
            }
        }
    }

    private function _getNotifications()
    {
        // vues
        $_SESSION['views'] = $this->model->Auth->countViews(User::getContextUser('id'));

        // Récupération & comptage des links
        $olLinks  = (!empty($_SESSION['links'])) ? $_SESSION['links'] : null;
        $newLinks = $this->model->Link->setContextUserLinks();
        if (!empty($olLinks) && $olLinks['count'][LINK_STATUS_RECIEVED] < $newLinks['count'][LINK_STATUS_RECIEVED]) {
            $this->view->growler('Nouvelle demande !', GROWLER_INFO);
        }

        // Vérification des nouveaux mails
        $oldMailsCount  = (!empty($_SESSION['new_mails'])) ? $_SESSION['new_mails'] : 0;
        $_SESSION['new_mails'] = $this->model->Auth->countNewMails(User::getContextUser('id'));
        if ($oldMailsCount < $_SESSION['new_mails']) {
            $this->view->growler('Nouveau message !', GROWLER_INFO);
        }
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

    // Vérifie la conformité de la session et les mails
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
                // Mail non validé
                session_destroy();
                $this->redirect('home', array('msg' => ERR_MAIL_NOT_VALIDATED));
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
