<?php

/*
 *  Classe d'accès aux données des mails
 */
class Message extends AppModel
{

    // Change l'état d'un mail
    public function updateMessageState($messageId, $messageStateId)
    {
        $sql = "UPDATE mail SET state_id = '".$this->securize($messageStateId)."'
                WHERE message_id = '".$this->securize($messageId)."'";

        return $this->execute($sql);
    }

    // Récupère l'ensemble de la conversation
    public function getConversation($userId, $offset = 0)
    {
        $start = ($offset * NB_RESULTS);
        $stop  = NB_RESULTS;

        $sql = '
            SELECT
                message_id,
                user.user_id as user_id,
                expediteur,
                destinataire,
                user_login,
                user_gender,
                content,
                UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                date,
                UNIX_TIMESTAMP( date ) AS delais,
                user_photo_url,
                state_libel,
                mail.state_id as state_id
            FROM
                mail
            JOIN user ON (user.user_id = mail.expediteur)
            JOIN ref_state ON (ref_state.state_id = mail.state_id)
            WHERE expediteur IN (:context_user_id, :user_id)
            AND destinataire IN (:context_user_id, :user_id)
            AND mail.state_id IN (:status_sent, :status_read)
            ORDER BY date DESC
            LIMIT ' . $start . ', ' . $stop . '
            ;
        ';

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue('context_user_id', User::getContextUser('id'));
        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('status_sent', STATUS_SENT);
        $stmt->bindValue('status_read', STATUS_READ);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Supprime un mail
    public function deleteMessageById($messageId)
    {
        $sql = "DELETE FROM mail WHERE message_id = '".$this->securize($messageId)."';";
        $resultat = $this->execute($sql, false);
        return $resultat;
    }

    // Supprime toute une conversation
    public function deleteConversation($linkId)
    {
        $catchSql = "SELECT * FROM mail WHERE message_id = '".$this->securize($linkId)."';";
        $catch = $this->fetchOnly($catchSql);

        $sql = "DELETE FROM mail
                WHERE destinataire IN ('".$catch['destinataire']."', '".$catch['expediteur']."')
                AND expediteur IN ('".$catch['destinataire']."', '".$catch['expediteur']."')
                AND state_id != ".STATUS_ADMIN;
        return $this->execute($sql);
    }
}
