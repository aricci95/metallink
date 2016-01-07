<?php

/*
 *  Classe d'accès aux données des authentifications
 */

class Auth extends AppModel
{

    // Lance une session pour OVH
    public function startSession()
    {

    }

    // Compter le nombre de nouveaux messages reçus
    public function countNewMessages()
    {
        $sql = "SELECT count(*) as nbr
    			FROM
    				message
    			WHERE
    				destinataire_id = :context_user_id
    			AND state_id = :message_status_sent
                AND expediteur_id NOT IN (
                    SELECT destinataire_id FROM link WHERE status = :link_status_blacklist
                    AND expediteur_id = :context_user_id
                )
            ;";

        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('context_user_id', User::getContextUser('id'), PDO::PARAM_INT);
        $stmt->bindValue('message_status_sent', MESSAGE_STATUS_SENT, PDO::PARAM_INT);
        $stmt->bindValue('link_status_blacklist', LINK_STATUS_BLACKLIST, PDO::PARAM_INT);

        $resultat = Db::executeStmt($stmt)->fetch();

        return $resultat['nbr'];
    }

    public function getLastMessage()
    {
        $sql = "message_id,
                    user.user_id as user_id,
                    expediteur_id,
                    destinataire_id,
                    user_login,
                    user_gender,
                    content,
                    date,
                    UNIX_TIMESTAMP( date ) AS delais,
                    user_photo_url,
                FROM
                    message,
                    user
                WHERE user_id = expediteur_id
                AND user.user_id = message.destinataire_id;";
        return $this->fetchOnly($sql);
    }

    // Compter le nombre de demandes link
    public function countLinkRequests()
    {
        $sql = "SELECT count(*) as nbr
    			FROM
    				link
    			WHERE
    				destinataire_id = '".User::getContextUser('id')."'
    				AND status = ".LINK_STATUS_SENT.";";

        $resultat = $this->fetchOnly($sql);

        return $resultat['nbr'];
    }

    public function countLinks()
    {
        $sql = "SELECT count(*) as nbr
    			FROM
    				link
    			WHERE status = ".LINK_STATUS_ACCEPTED."
    			AND destinataire_id = '".User::getContextUser('id')."'
    			OR	expediteur_id = '".User::getContextUser('id')."'
    			AND status = ".LINK_STATUS_ACCEPTED.";";
        $resultat = $this->fetchOnly($sql);
        return $resultat['nbr'];
    }

    public function countViews()
    {
        $sql = "SELECT count(*) as nbr
    			FROM
    				user_views
    			WHERE viewed_id = '".User::getContextUser('id')."'
                AND viewer_id NOT IN (
                    SELECT destinataire_id FROM link
                    WHERE status = ".LINK_STATUS_BLACKLIST."
                    AND expediteur_id = '".User::getContextUser('id')."')
                AND viewer_id != ".User::getContextUser('id');
        $resultat = $this->fetchOnly($sql);
        return $resultat['nbr'];
    }

    // Compter le nombre de demandes link validés
    public function countBlacklist()
    {
        $sql = "SELECT count(*) as nbr
                FROM
                    link
                WHERE
                    expediteur_id = '".User::getContextUser('id')."'
                    AND status = ".LINK_STATUS_BLACKLIST.";";
        $resultat = $this->fetchOnly($sql);
        return $resultat['nbr'];
    }

    public function checkLogin($login, $pwd)
    {
        if (!empty($login) && !empty($pwd)) {
            $sql = '
                SELECT
                    user_id,
                    user_login,
                    role_id,
                    user_photo_url,
                    FLOOR((DATEDIFF( CURDATE(), (user_birth))/365)) AS age,
                    user_gender,
                    user_valid,
                    user_city,
                    user_zipcode,
                    user_mail,
                    longitude,
                    lattitude,
                    forum_notification
                FROM user LEFT JOIN ville ON (user.user_zipcode = ville.code_postal)
                WHERE LOWER(user_login) = LOWER(:user_login)
                AND user_pwd = :pwd
            ;';

            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('user_login', $login);
            $stmt->bindValue('pwd', md5($pwd));
            $stmt->execute();
            $user = $stmt->fetch();

            if (!empty($user['user_login']) && !empty($user['user_id']) && strtolower($user['user_login']) == strtolower($login) && $login != '') {
                $sql = '
                    UPDATE
                        user
                    SET
                        user_last_connexion = NOW()
                    WHERE
                        LOWER(user_login) = LOWER(:login)
                ;';

                $this->execute($sql, array('login' => $login));

                if ($user['user_valid'] != 1) {
                    throw new Exception("Email non validé", ERR_MAIL_NOT_VALIDATED);
                } elseif ($user['role_id'] > 0) {
                    $_SESSION['user_id']             = $user['user_id'];
                    $_SESSION['user_login']          = $user['user_login'];
                    $_SESSION['user_pwd']            = $pwd;
                    $_SESSION['user_last_connexion'] = time();
                    $_SESSION['role_id']             = $user['role_id'];
                    $_SESSION['user_photo_url']      = empty($user['user_photo_url']) ? 'unknowUser.jpg' : $user['user_photo_url'];
                    $_SESSION['age']         = $user['age'];
                    $_SESSION['user_valid']  = $user['user_valid'];
                    $_SESSION['user_mail']   = $user['user_mail'];
                    $_SESSION['user_gender'] = $user['user_gender'];
                    $_SESSION['user_city']   = $user['user_city'];
                    $_SESSION['user_zipcode']   = $user['user_zipcode'];
                    $_SESSION['user_longitude'] = $user['longitude'];
                    $_SESSION['user_lattitude'] = $user['lattitude'];
                    $_SESSION['forum_notification'] = $user['forum_notification'];

                    return true;
                }
            } else {
                throw new Exception("Mauvais login / mot de passe", ERR_LOGIN);
            }
        } else {
            throw new Exception("Mauvais login / mot de passe", ERR_LOGIN);
        }
        return false;
    }

    // Renvoi le mdp
    public function sendPwd($login = null, $message = null)
    {
        if (empty($login) && empty($message)) {
            return false;
        }

        $param = (empty($login)) ? 'user_mail' : 'user_login';
        $value = (empty($login)) ? $message : $login;

        $result = $this->fetchOnly("SELECT user_id, user_login, user_mail FROM user WHERE ".$param." = '".$value."'");

        if (!empty($result['user_login'])) {
            $pwd_valid = uniqid();

            $insertQuery = "
                REPLACE INTO lost_pwd (
                    user_id,
                    pwd_valid
                ) VALUES (
                    " . $result['user_id'] . ",
                    '" . $pwd_valid . "'
                )
            ;";

            try {
                $this->execute($insertQuery);
            } catch (Exception $e) {
                return false;
            }

            $message = 'Pour modifier ton mot de passe clique sur le lien suivant : <a href="http://www.metallink.fr/lostpwd/new/' . $pwd_valid . '">modifier mon mot de passe</a>';

            return Mailer::send($result['user_mail'], 'Modifcation du mot de passe MetalLink', $message);
        } else {
            return false;
        }
    }

    public function updatePwd($pwd, $pwd_valid)
    {
        if (empty($pwd) || empty($pwd_valid)) {
            return false;
        }

        $query = "
            UPDATE user SET user_pwd = '" . md5($pwd) . "'
            WHERE user_id = (
                SELECT user_id FROM lost_pwd
                WHERE pwd_valid = '" . $pwd_valid . "'
            )
        ;";

        return $this->execute($query);
    }
}
