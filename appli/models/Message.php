<?php

/*
 *  Classe d'accès aux données des messages
 */
class Message extends AppModel
{

    // Change l'état d'un message
    public function updateMessageState($messageId, $messageStateId)
    {
        $sql = "UPDATE message SET state_id = '".$this->securize($messageStateId)."'
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
                message.state_id as state_id
            FROM
                message
            JOIN user ON (user.user_id = message.expediteur)
            JOIN ref_state ON (ref_state.state_id = message.state_id)
            WHERE expediteur IN (:context_user_id, :user_id)
            AND destinataire IN (:context_user_id, :user_id)
            AND message.state_id IN (:status_sent, :status_read)
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

    // Supprime un message
    public function deleteMessageById($messageId)
    {
        $sql = "DELETE FROM message WHERE message_id = '".$this->securize($messageId)."';";
        $resultat = $this->execute($sql, false);
        return $resultat;
    }

    // Supprime toute une conversation
    public function deleteConversation($linkId)
    {
        $catchSql = "SELECT * FROM message WHERE message_id = '".$this->securize($linkId)."';";
        $catch = $this->fetchOnly($catchSql);

        $sql = "DELETE FROM message
                WHERE destinataire IN ('".$catch['destinataire']."', '".$catch['expediteur']."')
                AND expediteur IN ('".$catch['destinataire']."', '".$catch['expediteur']."')
                AND state_id != ".STATUS_ADMIN;
        return $this->execute($sql);
    }
}
