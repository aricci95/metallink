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
    public function countNewMails()
    {
        $sql      = "SELECT count(*) as nbr
    			FROM 
    				mail
    			WHERE
    				mail_destinataire = '".$this->getContextUser('id')."'
    			AND mail_state_id = 1
                AND mail_expediteur NOT IN (
                    SELECT destinataire_id FROM link WHERE status = ".LINK_STATUS_BLACKLIST." 
                     AND expediteur_id = '".$this->getContextUser('id')."'
                );";
        $resultat = $this->fetchOnly($sql);
        $return   = $resultat['nbr'];

        return $return;
    }

    public function getLastMail()
    {
        $sql = "mail_id,
                    user.user_id as user_id,
                    mail_expediteur,
                    mail_destinataire,
                    user_login,
                    user_gender,
                    mail_content, 
                    mail_date, 
                    UNIX_TIMESTAMP( mail_date ) AS mail_delais,
                    user_photo_url,
                FROM 
                    mail,
                    user
                WHERE user_id = mail_expediteur
                AND user.user_id = mail.mail_destinataire;";
        return $this->fetchOnly($sql);
    }

    // Compter le nombre de demandes link
    public function countLinkRequests()
    {
        $sql = "SELECT count(*) as nbr
    			FROM 
    				link
    			WHERE
    				destinataire_id = '".$this->getContextUser('id')."'
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
    			AND destinataire_id = '".$this->getContextUser('id')."'  
    			OR	expediteur_id = '".$this->getContextUser('id')."'
    			AND status = ".LINK_STATUS_ACCEPTED.";";
        $resultat = $this->fetchOnly($sql);
        return $resultat['nbr'];
    }
    
    public function countViews()
    {
        $sql = "SELECT count(*) as nbr
    			FROM 
    				user_views
    			WHERE viewed_id = '".$this->getContextUser('id')."'
                AND viewer_id NOT IN (
                    SELECT destinataire_id FROM link 
                    WHERE status = ".LINK_STATUS_BLACKLIST." 
                    AND expediteur_id = '".$this->getContextUser('id')."')
                AND viewer_id != ".$this->getContextUser('id');
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
                    expediteur_id = '".$this->getContextUser('id')."'
                    AND status = ".LINK_STATUS_BLACKLIST.";";
        $resultat = $this->fetchOnly($sql);
        return $resultat['nbr'];
    }

    public function checkLogin($login, $pwd)
    {
        if (!empty($login) && !empty($pwd)) {
            $sql = "SELECT 
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
                            lattitude
                    FROM user LEFT JOIN ville ON (user.user_zipcode = ville.code_postal)
                    WHERE LOWER(user_login) = LOWER('".$this->securize($login, false)."')
                    AND user_pwd = '".$this->securize(md5($pwd), false)."'";
            $user = $this->fetchOnly($sql);
            if (!empty($user['user_login']) && !empty($user['user_id']) && strtolower($user['user_login']) == strtolower($login) && $login != '') {
                $sql = "UPDATE user SET user_last_connexion = '" . date("Y-m-d H:m:s") . "'
                        WHERE LOWER(user_login) = LOWER('" . $this->securize($login, false) . "')";
                $this->execute($sql);
                if ($user['user_valid'] != 1) {
                    throw new Exception("Email non validé", ERR_MAIL_NOT_VALIDATED);
                } elseif ($user['role_id'] > 0) {
                    $_SESSION['user_id']             = $user['user_id'];
                    $_SESSION['user_login']          = $user['user_login'];
                    $_SESSION['user_pwd']            = $pwd;
                    $_SESSION['user_last_connexion'] = time();
                    $_SESSION['role_id']             = $user['role_id'];
                    $_SESSION['user_photo_url']      = empty($user['user_photo_url']) ? 'unknowUser.jpg' : $user['user_photo_url'];
                    //$_SESSION['user_photo_id']       = $user['photo_id'];
                    $_SESSION['age']         = $user['age'];
                   // $_SESSION['user_statut'] = $user['user_statut'];
                    $_SESSION['user_valid']  = $user['user_valid'];
                    $_SESSION['user_mail']   = $user['user_mail'];
                    $_SESSION['user_gender'] = $user['user_gender'];
                    $_SESSION['user_city']   = $user['user_city'];
                    $_SESSION['user_zipcode']   = $user['user_zipcode'];
                    $_SESSION['user_longitude'] = $user['longitude'];
                    $_SESSION['user_lattitude'] = $user['lattitude'];
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
    public function sendPwd($login = null, $mail = null)
    {
        if (empty($login) && empty($mail)) {
            return false;
        }
        $param = (empty($login)) ? 'user_mail' : 'user_login';
        $value = (empty($login)) ? $mail : $login;
        $result = $this->fetchOnly("SELECT user_login, user_mail FROM user WHERE ".$param." = '".$value."'");
        if (!empty($result['user_login'])) {
            $newPwd = uniqid();
            $this->execute("UPDATE user SET user_pwd = '".md5($newPwd)."' WHERE ".$param." = '".$value."'");
            $message = 'Identifiant : <b>'.$result['user_login'].'</b><br/><br/>Nouveau mot de passe : <b>' . $newPwd . '</b><br/><br/>Pensez à le modifier dans votre profil.';
            return $this->load('mailer')->send($result['user_mail'], 'Nouveau mot de passe MetalLink', $message);
        } else {
            return false;
        }
    }
}
