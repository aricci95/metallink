<?php

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
                    expediteur_id,
                    state_libel,
                    UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                    message.state_id as state_id,
                    LEFT(content, 300) as content,
                    UNIX_TIMESTAMP( date ) AS delais,
                    user_photo_url
                FROM
                    message JOIN user ON (message.expediteur_id = user.user_id)
                            JOIN ref_state ON (ref_state.state_id = message.state_id)
                WHERE
                     destinataire_id = '". User::getContextUser('id') ."'
                     AND user_id NOT IN (
                            SELECT destinataire_id FROM link
                            WHERE expediteur_id = '".User::getContextUser('id')."'
                            AND status = ".LINK_STATUS_BLACKLIST."
                        )
                ORDER BY date DESC
                LIMIT ".($offset * NB_MAILBOX_RESULTS).", ".NB_MAILBOX_RESULTS.";";

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

    // Recupere la liste des messages recus
    public function getSentMessage($userId, $offset = 0)
    {
        $userId = $this->securize($userId);

        $sql = "SELECT
					message.message_id as message_id,
					state_libel,
					message.state_id as state_id,
					expediteur_id,
					user_gender,
					user.user_id as user_id,
					date,
					UNIX_TIMESTAMP( date ) AS delais,
					destinataire_id,
					LEFT(content, 50) as content,
					message.state_id as state_id,
					user_login,
                    UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
					user_photo_url
					FROM
						message,
						mailbox,
						ref_state,
						user
					WHERE message.mailbox_id = mailbox.mailbox_id
					AND message.state_id = ref_state.state_id
					AND message.expediteur_id = user.user_id
        			AND destinataire_id = '$userId'
        			AND message.state_id NOT IN ('".STATUS_DELETED."') ";

        $sql .= " GROUP BY expediteur ";
        $sql .= " ORDER BY date ASC";
        $sql .= ' LIMIT '.($offset * NB_MAILBOX_RESULTS).', '.NB_MAILBOX_RESULTS.';';
        return $this->fetch($sql);
    }

    public function deleteConversation($userId)
    {
        $sql = "DELETE FROM message
				WHERE (expediteur_id = '".$this->securize($userId)."' OR destinataire_id = '".$this->securize($userId)."')
				AND (expediteur_id = '".User::getContextUser('id')."' OR destinataire_id = '".User::getContextUser('id')."');";
        $this->execute($sql);
    }
}
