<?php

class Forum extends AppModel
{

    public function getLastMessage()
    {
        $sql = 'SELECT id, content, user_login, DATE_FORMAT(date,\'%H:%i\') as date FROM forum
                WHERE user_id != :context_user_id
                AND (UNIX_TIMESTAMP( NOW() ) - UNIX_TIMESTAMP( date )) < :time_limit
                ORDER BY date DESC
                LIMIT 0, 1
            ;';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('context_user_id', $this->context->get('user_id'));
        $stmt->bindValue('time_limit', 1000);

        return $this->db->executeStmt($stmt)->fetch();
    }

    public function getLastMessages($messageId = 0)
    {
        $sql = 'SELECT
                    id,
                    content,
                    user_login,
                    DATE_FORMAT(date,\'%H:%i\') as date
                FROM
                    forum
                WHERE
                    id > :message_id
                ORDER BY id DESC
                LIMIT 0,50;
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('message_id', $messageId, PDO::PARAM_INT);

        $messages = $this->db->executeStmt($stmt)->fetchAll();

        // Ne pas afficher les message dupliqués
        $previousMessage = array('content' => '');

        foreach ($messages as $index => $message) {
            if ($message['content'] == $previousMessage['content']) {
                unset($messages[$index]);
            } else {
                $previousMessage = $message;
            }
        }

        return array_reverse($messages);
    }

    public function getConnectedUsers()
    {
        $sql = 'SELECT  user.user_id as user_id,
                        user_login,
                        user_gender
                FROM user
                WHERE user.user_id NOT
                    IN (SELECT expediteur_id FROM
                        link WHERE status = '.LINK_STATUS_BLACKLIST.'
                        AND destinataire_id = '.$this->context->get('user_id').')
                AND (UNIX_TIMESTAMP( NOW() ) - UNIX_TIMESTAMP( user_last_connexion )) < '.ONLINE_TIME_LIMIT.'
                ORDER BY user_login ASC
                LIMIT 0, 100;';

         return $this->fetch($sql);
    }
}
