<?php

/*
 *  Classe d'acc�s aux donn�s des utilisateurs
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

    public static function getContextUser($attribute = null)
    {
        if (!empty($attribute) && array_key_exists('user_'.$attribute, $_SESSION) && !empty($_SESSION['user_'.$attribute])) {
            if ($attribute == 'id') {
                return (int) $_SESSION['user_id'];
            } else {
                return $_SESSION['user_'.$attribute];
            }
        } elseif ($attribute == 'role_id' && !empty($_SESSION['role_id'])) {
            return $_SESSION['role_id'];
        } else {
            if (!empty($_SESSION['user_id'])) {
                return array('id' => $_SESSION['user_id'],
                            'login' => $_SESSION['user_login'],
                            'last_connexion' => $_SESSION['user_last_connexion'],
                            'role_id' => $_SESSION['role_id'],
                            'photo_url' => empty($_SESSION['user_photo_url']) ? 'unknowUser.jpg' : $_SESSION['user_photo_url'],
                            'age' => $_SESSION['age'],
                            'gender' => $_SESSION['user_gender'],
                            'city'   => $_SESSION['user_city'],
                            'zipcode'   => $_SESSION['user_zipcode']);
            }
        }

        return null;
    }

    public function getUsers()
    {
        return $this->fetch("SELECT * FROM user ORDER BY user_login");
    }

    // Mets � jour la date de connexion
    public function updateLastConnexion()
    {
        $_SESSION['user_last_connexion'] = time();
        $this->execute('INSERT INTO user_statuses (user_id, status) VALUES ('.User::getContextUser('id').', 1) ON DUPLICATE KEY UPDATE status = 1');
        $sql = 'UPDATE user SET user_last_connexion = NOW() WHERE user_id ='.User::getContextUser('id');
        return $this->execute($sql);
    }

    // R�cup�res les utilisateurs par crit�res
    public function getSearch($criterias, $offset = 0)
    {
        $contextUserId = User::getContextUser('id');
        if (empty($contextUserId)) {
            throw new Exception('Context user manquant ', ERROR_BEHAVIOR);
        }

        $sql = 'SELECT
                    user_id,
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
                        link WHERE status = '.LINK_STATUS_BLACKLIST.'
                        AND destinataire_id = '.$contextUserId.')
                AND user_valid = 1
                AND user_id != "'.$contextUserId.'" ';
        if (!empty($criterias['search_login'])) {
            $sql .= " AND user_login LIKE '%".$criterias['search_login']."%' ";
        }
        if (!empty($criterias['search_gender'])) {
            $sql .= " AND user_gender = '".$criterias['search_gender']."' ";
        }
        if (!empty($criterias['search_age'])) {
            $sql .= "AND FLOOR((DATEDIFF( CURDATE(), (user_birth))/365)) >= ".$criterias['search_age']." ";
        }
        if (!empty($criterias['search_distance'])) {
            $longitude = User::getContextUser('longitude');
            $lattitude = User::getContextUser('lattitude');
            if (!is_array($longitude) && !is_array($lattitude)) {
                if ($longitude > 0 && $lattitude > 0) {
                    // On r�cup�re les codes postaux associ�s
                    $proxSql = "SELECT distinct LEFT(code_postal, 2) as code_postal FROM ville
                            WHERE (6366*acos(cos(radians(".$lattitude."))*cos(radians(`lattitude`))*cos(radians(`longitude`)
                            -radians(".$longitude."))+sin(radians(".$lattitude."))*sin(radians(`lattitude`)))) <= ".($criterias['search_distance'] / 10);
                    $closeCPs = $this->fetch($proxSql);
                    if (count($closeCPs) > 0) {
                        $sql .= ' AND LEFT(user_zipcode, 2) IN (';
                        foreach ($closeCPs as $ville) {
                            $sql .= "'".$ville['code_postal']."', ";
                        }
                        $sql .= ") ";
                    }
                    $sql .= ' AND user_zipcode IS NOT null ';
                }
            }
        }
        $sql .= ' ORDER BY user_last_connexion DESC
         LIMIT '.($offset * NB_SEARCH_RESULTS).', '.NB_SEARCH_RESULTS.';';


        $sql = str_replace('WHERE ORDER', ' ORDER', $sql);
        $sql = str_replace(',)', ')', $sql);
        $sql = str_replace(', )', ')', $sql);
        $sql = str_replace('AND ORDER', ' ORDER', $sql);
        $resultat = $this->fetch($sql);
        return $resultat;
    }

    // Calcule l'age de l'utilisateur
    public function getUserAge($userBirthDate)
    {
        $year = $userBirthDate[0].$userBirthDate[1].$userBirthDate[2].$userBirthDate[3];
        $today = date("Y");
        $age = (int)$today - (int)$year;

        return $age;
    }

    // Convertis les 1 et 0 en oui et non
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

    // Convertis les quantit�s
    public function convertQuantities($user)
    {
        $liste = array('drugs', 'alcohol', 'smoke');
        $quantities = array('', 'jamais', 'pas beaucoup', 'mod�r�ment', 'souvent', 'tr�s souvent');
        foreach ($liste as $key => $value) {
            if (isset($quantities[$user['user_'.$value]])) {
                $resultat[$value] = $quantities[$user['user_'.$value]];
            }
        }
        if (isset($resultat)) {
            return $resultat;
        }
    }

    // Compte le nombre d'utilisateurs
    public function countUsers()
    {
        $sql = "SELECT count(*) as number
                FROM user
                GROUP BY user_gender;";

        $resultat = $this->fetch($sql);
        return $resultat;
    }

    // R�cup�re la liste des utilisateurs
    public function getNew()
    {
        $userId = User::getContextUser('id');

        $sql = '
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
        ';

        if (!empty($userId)) {
            $sql .= ' WHERE user_id NOT IN (SELECT destinataire_id FROM link WHERE status = :linkStatusBlacklist AND expediteur_id = :contextUserId) ';
        }

        $sql .= 'ORDER BY user_subscribe_date DESC
                 LIMIT 0, 3;';

        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('linkStatusBlacklist', LINK_STATUS_BLACKLIST);

        if (!empty($userId)) {
            $stmt->bindValue('contextUserId', $userId);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    // R�cup�re un utilisateur
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

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue('user_id', (int) $userId);

        $user = Db::executeStmt($stmt)->fetch();

        return $this->_injectUserData($user);
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

    // R�cup�re un utilisateur
    public function getById($userId)
    {
        $sql = "SELECT
                    user.user_id as user_id,
                    user_login,
                    user_gender,
                    user_photo_url,
                    user_profession,
                    user_light_description,
                    UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                    user_description,
                    role_id,
                    user_valid,
                    user_pwd,
                    user_mail,
                    user_city,
                    ville_id,
                    user_zipcode,
                    FLOOR((DATEDIFF( CURDATE(), (user_birth))/365)) AS age,
                    style_id
                FROM
                    user
                WHERE user_id = :user_id
            ;";

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);

        return Db::executeStmt($stmt)->fetch();
    }

    public static function deleteById($id)
    {
        $sql = "DELETE FROM user WHERE user_id = :id;
                DELETE FROM user_views WHERE viewer_id = :id OR viewed_id = :id;
                DELETE FROM message WHERE destinataire_id = :id OR expediteur = :id;
                DELETE FROM chat WHERE `from` = :user_login OR `to` = :user_login;
            ";

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->bindValue('user_login', $id, PDO::PARAM_STR);

        return Db::executeStmt($stmt);
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
            $sql .= ' WHERE user_id = '.$this->securize(User::getContextUser('id'));
            $sql = str_replace(', WHERE', ' WHERE', $sql);

            return $this->execute($sql);
        }

    }

    public function setValid($code)
    {
        $sql = "UPDATE user SET user_valid = 1 WHERE user_valid = '$code'";
        return $this->execute($sql);
    }

    public function isUsedLogin($login)
    {
        $sql = "SELECT user_id
                FROM   user
                WHERE  user_login = '".$login."'";
        $result = $this->fetchOnly($sql);
        return !empty($result);
    }

    public function isUsedMessage($message)
    {
        $sql = "SELECT user_id
                FROM   user
                WHERE  user_mail = '".$message."'";
        $result = $this->fetchOnly($sql);
        return !empty($result);
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

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue('user_login', $items['user_login']);
        $stmt->bindValue('user_pwd', $items['user_pwd']);
        $stmt->bindValue('user_mail', $items['user_mail']);
        $stmt->bindValue('user_gender', $items['user_gender']);
        $stmt->bindValue('role_id', AUTH_LEVEL_USER);
        $stmt->bindValue('user_valid', $userValidationId);

        if ($stmt->execute()) {
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

            $stmt = Db::getInstance()->prepare($sql);

            $stmt->bindValue('expediteur_id', 1);
            $stmt->bindValue('destinataire_id', $this->insertId());
            $stmt->bindValue('status', LINK_STATUS_ACCEPTED);

            $stmt->execute();
        }

        return $userValidationId;
    }

    // R�cup�re le message d'un user
    public function getMessageByUser($userId)
    {
        $sql = "SELECT user_mail, user_login FROM user WHERE user_id = '".$this->securize($userId)."'";
        $resultat = $this->fetchOnly($sql);
        return $resultat;
    }
}
