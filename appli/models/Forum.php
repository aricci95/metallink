<?php

class Forum extends AppModel
{

    public static function getLastMessage()
    {
        $sql = 'SELECT id, content, user_login, DATE_FORMAT(date,\'%H:%i\') as date FROM forum
                WHERE user_id != :context_user_id
                AND (UNIX_TIMESTAMP( NOW() ) - UNIX_TIMESTAMP( date )) < :time_limit
                ORDER BY date DESC
                LIMIT 0, 1
            ;';

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue('context_user_id', User::getContextUser('id'));
        $stmt->bindValue('time_limit', 1000);

        return Db::executeStmt($stmt)->fetch();
    }

    public function getLastMessages($messageId = null)
    {
        $sql = 'SELECT  id, content, user_login, DATE_FORMAT(date,\'%H:%i\') as date
                FROM forum
                WHERE user_id NOT
                    IN (SELECT expediteur_id FROM
                        link WHERE status = '.LINK_STATUS_BLACKLIST.'
                        AND user_id = '.User::getContextUser('id').') ';
        if (!empty($messageId)) {
            $sql .= 'AND id > '.$messageId;
        }
        $sql .= ' ORDER BY id DESC
                  LIMIT 0,50';

        return array_reverse($this->fetch($sql));
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
                        AND destinataire_id = '.User::getContextUser('id').')
                AND (UNIX_TIMESTAMP( NOW() ) - UNIX_TIMESTAMP( user_last_connexion )) < '.ONLINE_TIME_LIMIT.'
                ORDER BY user_login ASC
                LIMIT 0, 100;';

         return $this->fetch($sql);
    }
}
