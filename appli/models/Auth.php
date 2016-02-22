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

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('context_user_id', $this->context->get('user_id'), PDO::PARAM_INT);
        $stmt->bindValue('message_status_sent', MESSAGE_STATUS_SENT, PDO::PARAM_INT);
        $stmt->bindValue('link_status_blacklist', LINK_STATUS_BLACKLIST, PDO::PARAM_INT);

        $resultat = $this->db->executeStmt($stmt)->fetch();

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
    				destinataire_id = '".$this->context->get('user_id')."'
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
    			AND destinataire_id = '" . $this->context->get('user_id') . "'
    			OR	expediteur_id = '" . $this->context->get('user_id') . "'
    			AND status = ".LINK_STATUS_ACCEPTED.";";
        $resultat = $this->fetchOnly($sql);
        return $resultat['nbr'];
    }

    public function countViews()
    {
        $sql = "SELECT count(*) as nbr
    			FROM
    				user_views
    			WHERE viewed_id = '".$this->context->get('user_id')."'
                AND viewer_id NOT IN (
                    SELECT destinataire_id FROM link
                    WHERE status = ".LINK_STATUS_BLACKLIST."
                    AND expediteur_id = '".$this->context->get('user_id')."')
                AND viewer_id != ".$this->context->get('user_id');
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
                    expediteur_id = '".$this->context->get('user_id')."'
                    AND status = ".LINK_STATUS_BLACKLIST.";";
        $resultat = $this->fetchOnly($sql);
        return $resultat['nbr'];
    }

    public function resetPwd($userId)
    {
        $pwd_valid = uniqid();

        $sql = "
            REPLACE INTO lost_pwd (
                user_id,
                pwd_valid
            ) VALUES (
                :user_id,
                :pwd_valid
            )
        ;";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue('pwd_valid', $pwd_valid, PDO::PARAM_STR);

        if ($this->db->executeStmt($stmt)) {
            return $pwd_valid;
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
