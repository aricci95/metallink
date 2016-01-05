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

    public function getInboxMessage($offset = 0)
    {
        $sql = "SELECT
                    user.user_id as user_id,
                    message.message_id as message_id,
                    user_login,
                    user_gender,
                    expediteur,
                    state_libel,
                    UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                    mail.state_id as state_id,
                    LEFT(content, 300) as content,
                    UNIX_TIMESTAMP( date ) AS delais,
                    user_photo_url
                FROM
                    mail JOIN user ON (mail.expediteur = user.user_id)
                         JOIN ref_state ON (ref_state.state_id = mail.state_id)
                WHERE
                     destinataire = '".User::getContextUser('id')."'
                     AND user_id NOT IN (
                            SELECT destinataire_id FROM link
                            WHERE expediteur_id = '".User::getContextUser('id')."'
                            AND status = ".LINK_STATUS_BLACKLIST."
                        )
                OR
                    mail.state_id = ".STATUS_ADMIN."
                ORDER BY date DESC
                LIMIT ".($offset * NB_MAILBOX_RESULTS).", ".NB_MAILBOX_RESULTS.";";
       // echo $sql;die();
        $rawResults = $this->fetch($sql);
        if (count($rawResults) > 0) {
            foreach ($rawResults as $key => $message) {
                if (!isset($results[$message['user_id']])) {
                    $results[$message['user_id']] = $message;
                }
            }
            return $results;
        } else {
            return array();
        }
    }

    // Recupere la liste des mails recus
    public function getSentMessage($userId, $offset = 0)
    {
        $userId = $this->securize($userId);
        $sql = "SELECT
					message.message_id as message_id,
					state_libel,
					mail.state_id as state_id,
					expediteur,
					user_gender,
					user.user_id as user_id,
					date,
					UNIX_TIMESTAMP( date ) AS delais,
					destinataire,
					LEFT(content, 50) as content,
					mail.state_id as state_id,
					user_login,
                    UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
					user_photo_url
					FROM
						mail,
						mailbox,
						ref_state,
						user
					WHERE mail.mailbox_id = mailbox.mailbox_id
					AND mail.state_id = ref_state.state_id
					AND mail.expediteur = user.user_id
        			AND destinataire = '$userId'
        			AND mail.state_id NOT IN ('".STATUS_DELETED."') ";

        $sql .= " GROUP BY expediteur ";
        $sql .= " ORDER BY date ASC";
        $sql .= ' LIMIT '.($offset * NB_MAILBOX_RESULTS).', '.NB_MAILBOX_RESULTS.';';
        return $this->fetch($sql);
    }

    public function deleteConversation($userId)
    {
        $sql = "DELETE FROM mail
				WHERE (expediteur = '".$this->securize($userId)."' OR destinataire = '".$this->securize($userId)."')
				AND (expediteur = '".User::getContextUser('id')."' OR destinataire = '".User::getContextUser('id')."')
                AND state_id != ".STATUS_ADMIN;
        $this->execute($sql);
    }
}
