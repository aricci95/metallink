<?php

/*
 *  Classe d'accés aux donnés du forum
 */
class Forum extends AppModel
{

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
