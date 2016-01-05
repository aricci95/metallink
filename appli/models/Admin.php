<?php

/*
 *  Classe d'administration
 */
class Admin extends AppModel
{

    public function deleteUnusedAccounts()
    {
        // récupération des comptes inutilisés
        $sql = "(SELECT user_id FROM user
                WHERE user_photo_url = ''
                AND DATEDIFF(CURDATE(), user_last_connexion) >= 365
                AND user_description = ''
                AND user_light_description = '')
                UNION
                (SELECT user_id FROM user
                WHERE DATEDIFF(CURDATE(), user_last_connexion) >= 365
                AND user_valid != 1)
                UNION
                (SELECT user_id FROM user
                WHERE DATEDIFF(CURDATE(), user_subscribe_date) >= 500
                AND user_last_connexion = '0000-00-00 00:00:00')";
        $users = $this->fetch($sql);

        foreach ($users as $key => $user) {
            self::userDestroy($user['user_id']);
        }
    }
}
