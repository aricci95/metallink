<?php
 
/*
 *  Classe d'administration
 */
class Admin extends AppModel
{
    
    function deleteUnusedAccounts()
    {
        // récupération des comptes inutilisés
        $sql = "(SELECT user_id FROM user
                WHERE user_photo_url = ''
                AND DATEDIFF(CURDATE(), user_last_connexion) >= 365
                AND user_description = ''
                AND user_light_description = '')
                UNION
                (SELECT user_id FROM user
                WHERE DATEDIFF(CURDATE(), user_last_connexion) >= 365
                AND user_valid != 1)
                UNION
                (SELECT user_id FROM user
                WHERE DATEDIFF(CURDATE(), user_subscribe_date) >= 500
                AND user_last_connexion = '0000-00-00 00:00:00')";
        $users = $this->fetch($sql);
        foreach ($users as $key => $user) {
            self::userDestroy($user['user_id']);
        }
    }

    function switchUser($userId)
    {
        require_once(ROOT_DIR.'/appli/models/User.php');
        $users = new User($this->_db);

        require_once(ROOT_DIR.'/appli/models/Auth.php');
        $auth = new Auth($this->_db);

        $user = $users->getById($userId);
        if (isset($user['user_id'])) {
            $_SESSION['user_id']        = $user['user_id'];
            $_SESSION['user_login']     = $user['user_login'];
            $_SESSION['role_id']        = 1;
            $_SESSION['user_photo_url'] = $user['user_photo_url'];
            $_SESSION['new_mails']      = $auth->countNewMails($user['user_id']);
            $_SESSION['age']            = $user['age'];
            $_SESSION['user_statut']    = $user['user_statut'];
            $_SESSION['user_valid']     = $user['user_valid'];
            $_SESSION['user_mail']      = $user['user_mail'];
            $_SESSION['user_gender']    = $user['user_gender'];

            $LinkRequests = $auth->countLinkRequests($user['user_id']);
            $links        = $auth->countLinks($user['user_id']);

            $_SESSION['new_links'] = $LinkRequests;
            $_SESSION['links']     = $links;

            return true;
        } else {
            return false;
        }
    }
}
