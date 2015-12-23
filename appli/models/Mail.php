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
    public function getAdminConversation()
    {
        $this->log->err('getAdminConversation');
        $sql = "SELECT
                    mail_id,
                    user.user_id as user_id,
                    mail_expediteur,
                    mail_destinataire,
                    user_login,
                    user_gender,
                    mail_content,
                    mail_date,
                    UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                    UNIX_TIMESTAMP( mail_date ) AS mail_delais,
                    user_photo_url,
                    mail_state_libel,
                    mail.mail_state_id as mail_state_id
                FROM
                    mail,
                    ref_mail_state,
                    user
                WHERE user.user_id = mail.mail_expediteur
                AND user.user_id = 1
                AND ref_mail_state.mail_state_id = mail.mail_state_id
                AND mail.mail_state_id = ".MAIL_STATUS_ADMIN."
                ORDER BY mail_date DESC
                LIMIT 0, 10;";

        return $this->fetch($sql);
    }

    // Récupère l'ensemble de la conversation
    public function getConversation($userId, $offset = 0)
    {
        $sql = "SELECT
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
            WHERE mail_expediteur IN (".User::getContextUser('id').", ".$userId.")
            AND mail_destinataire IN (".User::getContextUser('id').", ".$userId.")
            AND mail.mail_state_id IN (".MAIL_STATUS_SENT.", ".MAIL_STATUS_READ.", ".MAIL_STATUS_ADMIN.")
            ORDER BY mail_date DESC
            LIMIT ".($offset * NB_MAIL_RESULTS).", ".NB_MAIL_RESULTS.";";
        return $this->fetch($sql);
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
    public function sendMail($items, $status = MAIL_STATUS_SENT)
    {
        if (empty($items['mail_destinataire']) || empty($items['mail_expediteur'])) {
            $message = "<br/><br/>Valeurs en paramètres : <br/>";
            foreach ($items as $key => $value) {
                if (!empty($value)) {
                    $message .= $key.' => '.$value.'<br/>';
                }
            }
            throw new Exception('Erreur lors de la sauvegarde du mail, destinataire / expediteur manquant'.$message, ERROR_BEHAVIOR);
        } else {
            $sql = "INSERT INTO mail (mail_content, mail_expediteur, mail_destinataire, mail_date, mail_state_id, mailbox_id)
                    VALUES ('".$items['mail_content']."', "
                              .$items['mail_expediteur'].", "
                              .$items['mail_destinataire'].", NOW(), "
                              .$status.", 1);";
            return $this->execute($sql);
        }
    }
}
