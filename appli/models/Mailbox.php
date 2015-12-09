<?php

/* 
 *  Classe d'acces aux donnees des mails
 */
class Mailbox extends AppModel
{
    
    // Liste les types de mailbox
    public function getMailboxTypes()
    {
        $sql = "SELECT mailbox_type_id, mailbox_type_libel FROM ref_mailbox_type;";

        $resultat = $this->fetch($sql);

        return $resultat;
    }

    public function getInboxMail($offset = 0)
    {
        $sql = "SELECT 
                    user.user_id as user_id,
                    mail.mail_id as mail_id,
                    user_login,
                    user_gender,
                    mail_expediteur,
                    mail_state_libel,
                    UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                    mail.mail_state_id as mail_state_id,
                    LEFT(mail_content, 300) as mail_content,
                    UNIX_TIMESTAMP( mail_date ) AS mail_delais,
                    user_photo_url
                FROM
                    mail JOIN user ON (mail.mail_expediteur = user.user_id)
                         JOIN ref_mail_state ON (ref_mail_state.mail_state_id = mail.mail_state_id)
                WHERE
                     mail_destinataire = '".$this->getContextUser('id')."'
                     AND user_id NOT IN (
                            SELECT destinataire_id FROM link
                            WHERE expediteur_id = '".$this->getContextUser('id')."'
                            AND status = ".LINK_STATUS_BLACKLIST."
                        )
                OR 
                    mail.mail_state_id = ".MAIL_STATUS_ADMIN."
                ORDER BY mail_date DESC
                LIMIT ".($offset * NB_MAILBOX_RESULTS).", ".NB_MAILBOX_RESULTS.";";
       // echo $sql;die();
        $rawResults = $this->fetch($sql);
        if (count($rawResults) > 0) {
            foreach ($rawResults as $key => $mail) {
                if (!isset($results[$mail['user_id']])) {
                    $results[$mail['user_id']] = $mail;
                }
            }
            return $results;
        } else {
            return array();
        }
    }
    
    // Recupere la liste des mails recus
    public function getSentMail($userId, $offset = 0)
    {
        $userId = $this->securize($userId);
        $sql = "SELECT 
					mail.mail_id as mail_id,
					mail_state_libel,
					mail.mail_state_id as mail_state_id,
					mail_expediteur,
					user_gender,
					user.user_id as user_id,
					mail_date, 
					UNIX_TIMESTAMP( mail_date ) AS mail_delais,
					mail_destinataire,
					LEFT(mail_content, 50) as mail_content,
					mail.mail_state_id as mail_state_id,
					user_login,
                    UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
					user_photo_url
					FROM 
						mail, 
						mailbox, 
						ref_mail_state,
						user
					WHERE mail.mailbox_id = mailbox.mailbox_id
					AND mail.mail_state_id = ref_mail_state.mail_state_id
					AND mail.mail_expediteur = user.user_id
        			AND mail_destinataire = '$userId' 
        			AND mail.mail_state_id NOT IN ('".MAIL_STATUS_DELETED."') ";
        
        $sql .= " GROUP BY mail_expediteur ";
        $sql .= " ORDER BY mail_date ASC";
        $sql .= ' LIMIT '.($offset * NB_MAILBOX_RESULTS).', '.NB_MAILBOX_RESULTS.';';
        return $this->fetch($sql);
    }

    public function deleteConversation($userId)
    {
        $sql = "DELETE FROM mail 
				WHERE (mail_expediteur = '".$this->securize($userId)."' OR mail_destinataire = '".$this->securize($userId)."')
				AND (mail_expediteur = '".$this->getContextUser('id')."' OR mail_destinataire = '".$this->getContextUser('id')."')
                AND mail_state_id != ".MAIL_STATUS_ADMIN;
        $this->execute($sql);
    }
}
