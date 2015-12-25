<?php

/*
 *  Classe d'accès aux données des mails
 */
class Mail extends AppModel
{

    // Change l'état d'un mail
    public function updateMailState($mailId, $mailStateId)
    {
        $sql = "UPDATE mail SET mail_state_id = '".$this->securize($mailStateId)."'
                WHERE mail_id = '".$this->securize($mailId)."'";

        return $this->execute($sql);
    }

    // Récupère l'ensemble de la conversation
    public function getConversation($userId, $offset = 0)
    {
        $start = ($offset * NB_MAIL_RESULTS);
        $stop  = NB_MAIL_RESULTS;

        $sql = '
            SELECT
                mail_id,
                user.user_id as user_id,
                mail_expediteur,
                mail_destinataire,
                user_login,
                user_gender,
                mail_content,
                UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                mail_date,
                UNIX_TIMESTAMP( mail_date ) AS mail_delais,
                user_photo_url,
                mail_state_libel,
                mail.mail_state_id as mail_state_id
            FROM
                mail
            JOIN user ON (user.user_id = mail.mail_expediteur)
            JOIN ref_mail_state ON (ref_mail_state.mail_state_id = mail.mail_state_id)
            WHERE mail_expediteur IN (:context_user_id, :user_id)
            AND mail_destinataire IN (:context_user_id, :user_id)
            AND mail.mail_state_id IN (:status_sent, :status_read)
            ORDER BY mail_date DESC
            LIMIT ' . $start . ', ' . $stop . '
            ;
        ';

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue('context_user_id', User::getContextUser('id'));
        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('status_sent', MAIL_STATUS_SENT);
        $stmt->bindValue('status_read', MAIL_STATUS_READ);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Supprime un mail
    public function deleteMailById($mailId)
    {
        $sql = "DELETE FROM mail WHERE mail_id = '".$this->securize($mailId)."';";
        $resultat = $this->execute($sql, false);
        return $resultat;
    }

    // Supprime toute une conversation
    public function deleteConversation($linkId)
    {
        $catchSql = "SELECT * FROM mail WHERE mail_id = '".$this->securize($linkId)."';";
        $catch = $this->fetchOnly($catchSql);

        $sql = "DELETE FROM mail
                WHERE mail_destinataire IN ('".$catch['mail_destinataire']."', '".$catch['mail_expediteur']."')
                AND mail_expediteur IN ('".$catch['mail_destinataire']."', '".$catch['mail_expediteur']."')
                AND mail_state_id != ".MAIL_STATUS_ADMIN;
        return $this->execute($sql);
    }

    // Envoyer un mail
    public function sendMail($from, $to, $content)
    {
        if (empty($from) || empty($to)) {
            $message = "<br/><br/>Valeurs en paramètres : <br/>";
            throw new Exception('Erreur lors de la sauvegarde du mail, destinataire / expediteur manquant'.$message, ERROR_BEHAVIOR);
        }

        $sql = '
            INSERT INTO mail (
                mail_content,
                mail_expediteur,
                mail_destinataire,
                mail_date,
                mail_state_id,
                mailbox_id
            ) VALUES (
                :mail_content,
                :mail_expediteur,
                :mail_destinataire,
                NOW(),
                :mail_state_id,
                :mailbox_id
            );
        ';

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue('mail_content', $content);
        $stmt->bindValue('mail_expediteur', $from);
        $stmt->bindValue('mail_destinataire', $to);
        $stmt->bindValue('mail_state_id', MAIL_STATUS_SENT);
        $stmt->bindValue('mailbox_id', 1);

        if ($stmt->execute()) {
            $destinataire = User::findById($to, array('user_mail'));
            $message      = User::getContextUser('login').' vous a envoyé un nouveau message ! <a href="http://metallink.fr/mail/' . User::getContextUser('id') . '">Cliquez ici</a> pour le lire.';

            return Mailer::send($destinataire['user_mail'], 'Nouveau message sur MetalLink !', $message);
        }
    }
}
