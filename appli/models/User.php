<?php

/*
 *  Classe d'accés aux donnés des utilisateurs
 */
class User extends AppModel
{

    private $_attributes = array(
        'user_login',
        'user_pwd',
        'user_mail',
        'user_gender',
        'user_birth',
        'style_id',
        'user_city',
        'user_zipcode',
        'ville_id',
        'user_light_description',
        'user_description',
        'user_data',
    );

    private $_serialized = array(
        'user_profession',
        'user_poids',
        'user_taille',
        'user_tattoo',
        'user_piercing',
        'look_id',
        'user_smoke',
        'user_alcohol',
        'user_drugs',
    );

    // Mets à jour la date de connexion
    public function updateLastConnexion($userId = null)
    {
        if (empty($userId)) {
            $userId = $this->context->get('user_id');
        }
        $sql = 'UPDATE user SET user_last_connexion = NOW() WHERE user_id = :user_id';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);

        $this->db->executeStmt($stmt);

        return true;
    }

    // Récupéres les utilisateurs par critéres
    public function getSearch($criterias, $offset = 0)
    {
        $contextUserId = $this->context->get('user_id');

        $sql = 'SELECT user_id,
                    user_login,
                    user_city,
                    user_mail,
                    look_libel,
                    user_gender,
                    user_photo_url,
                    UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                    FLOOR((DATEDIFF( CURDATE(), (user_birth))/365)) AS age
                FROM user
                LEFT JOIN ref_look ON user.look_id = ref_look.look_id
                WHERE user_id NOT
                    IN (SELECT expediteur_id FROM
                        link WHERE status = :link_status_blacklist
                        AND destinataire_id = :context_user_id)
                AND user_valid = 1
                AND user_id != :context_user_id
            ';

        if (!empty($criterias['search_login'])) {
            $sql .= " AND user_login LIKE :search_login ";
        }

        if (!empty($criterias['search_gender'])) {
            $sql .= " AND user_gender = :user_gender ";
        }

        if (!empty($criterias['search_age'])) {
            $sql .= " AND FLOOR((DATEDIFF( CURDATE(), (user_birth))/365)) <= :search_age ";
        }

        if (!empty($criterias['search_distance'])) {
            $longitude = $this->context->get('user_longitude');
            $latitude = $this->context->get('user_latitude');

            $sql .= ' AND user_longitude BETWEEN :longitude_begin AND :longitude_end
                      AND user_latitude BETWEEN :latitude_begin AND :latitude_end
                      AND user_zipcode IS NOT null ';
        }

        $sql .= ' ORDER BY user_last_connexion DESC
                  LIMIT :limit_begin, :limit_end;';

        $sql = str_replace(',)', ')', $sql);
        $sql = str_replace(', )', ')', $sql);

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('context_user_id', $this->context->get('user_id'), PDO::PARAM_INT);
        $stmt->bindValue('link_status_blacklist', LINK_STATUS_BLACKLIST, PDO::PARAM_INT);

        if (!empty($criterias['search_login'])) {
            $stmt->bindValue('search_login', '%'. $criterias['search_login'] .'%', PDO::PARAM_STR);
        }

        if (!empty($criterias['search_gender'])) {
            $stmt->bindValue('user_gender', $criterias['search_gender'], PDO::PARAM_INT);
        }

        if (!empty($criterias['search_age'])) {
            $stmt->bindValue('search_age', $criterias['search_age'], PDO::PARAM_INT);
        }

        if (!empty($criterias['search_distance'])) {
            $ratio = COEF_DISTANCE * $criterias['search_distance'];

            $stmt->bindValue('longitude_begin', ($longitude - $ratio), PDO::PARAM_INT);
            $stmt->bindValue('longitude_end', ($longitude + $ratio), PDO::PARAM_INT);

            $stmt->bindValue('latitude_begin', ($latitude - $ratio), PDO::PARAM_INT);
            $stmt->bindValue('latitude_end', ($latitude + $ratio), PDO::PARAM_INT);
        }

        $stmt->bindValue('limit_begin', $offset * NB_SEARCH_RESULTS, PDO::PARAM_INT);
        $stmt->bindValue('limit_end', NB_SEARCH_RESULTS, PDO::PARAM_INT);

        return $this->db->executeStmt($stmt)->fetchAll();
    }

    public function convertBinaries($user)
    {
        $liste = array('tattoo', 'piercing');
        foreach ($liste as $key => $value) {
            if ($user['user_'.$value] == 1) {
                $resultat[$value] = 'oui';
            } else {
                $resultat[$value] = 'non';
            }
        }
        return $resultat;
    }

    public function convertQuantities($user)
    {
        $liste = array('drugs', 'alcohol', 'smoke');
        $quantities = array('', 'jamais', 'pas beaucoup', 'modérément', 'souvent', 'très souvent');
        foreach ($liste as $key => $value) {
            if (isset($quantities[$user['user_'.$value]])) {
                $resultat[$value] = $quantities[$user['user_'.$value]];
            }
        }
        if (isset($resultat)) {
            return $resultat;
        }
    }

    public function countUsers()
    {
        $sql = "SELECT count(*) as number
                FROM user
                GROUP BY user_gender;";

        $resultat = $this->fetch($sql);
        return $resultat;
    }

    // Récupére la liste des utilisateurs
    public function getNew()
    {
        $userId = $this->context->get('user_id');

        $sql = "
            SELECT
                user_id,
                user_login,
                user_city,
                ville_id,
                look_libel,
                user_gender,
                UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                user_mail,
                user_photo_url,
                FLOOR((DATEDIFF( CURDATE(), (user_birth))/365)) AS age
            FROM user
            LEFT JOIN ref_look ON user.look_id = ref_look.look_id
            WHERE user_photo_url != ''
        ";

        if (!empty($userId)) {
            $sql .= ' AND user_id NOT IN (SELECT destinataire_id FROM link WHERE status = :linkStatusBlacklist AND expediteur_id = :contextUserId) ';
        }

        $sql .= 'ORDER BY user_subscribe_date DESC
                 LIMIT 0, 3;';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('linkStatusBlacklist', LINK_STATUS_BLACKLIST);

        if (!empty($userId)) {
            $stmt->bindValue('contextUserId', $userId);
        }

        return $this->db->executeStmt($stmt)->fetchAll();
    }

    // Récupére un utilisateur
    public function getUserByIdDetails($userId)
    {
        $sql = "SELECT
                    user.user_id as user_id,
                    user_login,
                    user_pwd,
                    user_mail,
                    user_city,
                    ville_id,
                    user_zipcode,
                    FLOOR((DATEDIFF( CURDATE(), (user_birth))/365)) AS age,
                    UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                    user_birth,
                    user_photo_url,
                    user_gender,
                    user_light_description,
                    user_description,
                    style_id,
                    user_data
                FROM
                    user
                WHERE user_id = :user_id
            ;";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('user_id', (int) $userId);

        $user = $this->db->executeStmt($stmt)->fetch();

        if (!empty($user)) {
            return $this->_injectUserData($user);
        } else {
            return false;
        }
    }

    private function _injectUserData($user)
    {
        $data = unserialize($user['user_data']);

        foreach ($this->_serialized as $key) {
            $user[$key] = empty($data[$key]) ? 0 : $data[$key];
        }

        unset($user['user_data']);

        return $user;
    }

    public function deleteById($id)
    {
        $sql = "DELETE FROM user WHERE user_id = :id;
                DELETE FROM user_views WHERE viewer_id = :id OR viewed_id = :id;
                DELETE FROM message WHERE destinataire_id = :id OR expediteur_id = :id;
                DELETE FROM link WHERE destinataire_id = :id OR expediteur_id = :id;
                DELETE FROM chat WHERE `from` = :user_login OR `to` = :user_login;
            ";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->bindValue('user_login', $id, PDO::PARAM_STR);

        return $this->db->executeStmt($stmt);
    }

    // Modifie un utilisateur
    public function updateUserById(array $data = array())
    {
        if (!empty($data)) {
            // Update serialized data
            $serialize = array();

            foreach ($data as $attribute => $value) {
                if (in_array($attribute, $this->_serialized) && $value > 0) {
                    $serialize[$attribute] = $value;
                }
            }

            $data['user_data'] = serialize($serialize);

            $sql = 'UPDATE user SET ';
            foreach ($this->_attributes as $attribute) {
                if (!empty($data[$attribute])) {
                    $sql .= " ".$attribute." = '".$data[$attribute]."',";
                }
            }
            $sql .= ' WHERE user_id = '.$this->securize($this->context->get('user_id'));
            $sql = str_replace(', WHERE', ' WHERE', $sql);

            return $this->execute($sql);
        }

    }

    public function setValid($code)
    {
        $sql = 'UPDATE
                    user
                SET
                    user_valid = 1
                WHERE
                    user_valid = :code';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':code', $code, PDO::PARAM_STR);

        return $this->db->executeStmt($stmt);
    }

    public function isUsedLogin($login)
    {
        $sql = 'SELECT user_id
                FROM   user
                WHERE  user_login = :login';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('login', $login, PDO::PARAM_STR);

        return $this->db->executeStmt($stmt)->fetch();
    }

    public function isUsedEmail($email)
    {
        $sql = 'SELECT user_id
                FROM   user
                WHERE  user_mail = :email';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('email', $email, PDO::PARAM_STR);

        return $this->db->executeStmt($stmt)->fetch();
    }

    public function createUser($items)
    {
        $userValidationId = uniqid();

        $sql = '
            INSERT INTO user (
                user_login,
                user_pwd,
                user_mail,
                user_gender,
                user_subscribe_date,
                role_id,
                user_valid
              ) VALUES (
                :user_login,
                :user_pwd,
                :user_mail,
                :user_gender,
                NOW(),
                :role_id,
                :user_valid
            );
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('user_login', $items['user_login']);
        $stmt->bindValue('user_pwd', $items['user_pwd']);
        $stmt->bindValue('user_mail', $items['user_mail']);
        $stmt->bindValue('user_gender', $items['user_gender']);
        $stmt->bindValue('role_id', AUTH_LEVEL_USER);
        $stmt->bindValue('user_valid', $userValidationId);

        $this->db->executeStmt($stmt);

        $sql = '
            REPLACE INTO link (
                expediteur_id,
                destinataire_id,
                status,
                modification_date
            ) VALUES (
                :expediteur_id,
                :destinataire_id,
                :status,
                NOW()
            );
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('expediteur_id', 1, PDO::PARAM_INT);
        $stmt->bindValue('destinataire_id', $this->db->lastInsertId(), PDO::PARAM_INT);
        $stmt->bindValue('status', LINK_STATUS_ACCEPTED, PDO::PARAM_INT);

        $this->db->executeStmt($stmt);

        return $userValidationId;
    }

    public function findByLoginPwd($login, $pwd)
    {
        $sql = '
                SELECT
                    user_id,
                    user_pwd,
                    user_login,
                    role_id,
                    user_photo_url,
                    FLOOR((DATEDIFF( CURDATE(), (user_birth))/365)) AS age,
                    user_gender,
                    user_valid,
                    user_city,
                    user_zipcode,
                    user_mail,
                    forum_notification,
                    user_longitude,
                    user_latitude
                FROM user
                WHERE LOWER(user_login) = LOWER(:user_login)
                AND user_pwd = :pwd
            ;';

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue('user_login', $login);
            $stmt->bindValue('pwd', md5($pwd));

            return $this->db->executeStmt($stmt)->fetch();
    }

    public function findByEmail($email)
    {
        $sql = '
                SELECT
                    user_id,
                    user_pwd,
                    user_login,
                    role_id,
                    user_photo_url,
                    FLOOR((DATEDIFF( CURDATE(), (user_birth))/365)) AS age,
                    user_gender,
                    user_valid,
                    user_city,
                    user_zipcode,
                    user_mail,
                    longitude,
                    user.ville_id as ville_id,
                    latitude,
                    forum_notification
                FROM user LEFT JOIN ville ON (user.user_zipcode = ville.code_postal)
                WHERE LOWER(user_mail) = LOWER(:email)
            ;';

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue('email', $email);

            return $this->db->executeStmt($stmt)->fetch();
    }

    public function updateUserLocalization(array $data)
    {
        $sql = '
            UPDATE user SET
                user_last_connexion = NOW(),
                user_zipcode = :user_zipcode,
                user_city = :user_city,
                ville_id = (
                    SELECT ville_id FROM ville WHERE code_postal = :user_zipcode LIMIT 0, 1
                )
            WHERE user_id = :user_id
        ;';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('user_zipcode', $data['user_zipcode'], PDO::PARAM_INT);
        $stmt->bindValue('user_city', $data['user_city'], PDO::PARAM_STR);
        $stmt->bindValue('user_id', $data['user_id'], PDO::PARAM_INT);

        return $this->db->executeStmt($stmt);
    }

}
