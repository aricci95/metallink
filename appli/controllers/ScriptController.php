<?php

class ScriptController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_SUPERADMIN;

    public function render()
    {
        $methods = get_class_methods($this);
        $scripts = array();

        foreach ($methods as $method) {
            if (strstr($method, 'renderScript') != false) {
                $scripts[] = str_replace('render', '', $method);
            }
        }

        $this->view->scripts = $scripts;
        $this->view->setViewName('admin/wScript');
        $this->view->setTitle('Scripts');
        $this->view->render();
    }

    public function renderScriptCoordinates()
    {
        $done = 0;

        $users = $this->model->user->find();

        foreach ($users as $user) {
            if ($user['user_longitude'] > 0) {
                continue;
            }

            $ville = $this->model->city->find(array('ville_longitude_deg', 'ville_latitude_deg'), array('%ville_code_postal' => $user['user_zipcode']), array(), '0, 1');

            if (!empty($ville[0])) {
                $coordinates = array(
                    'user_longitude' => $ville[0]['ville_longitude_deg'],
                    'user_latitude' => $ville[0]['ville_latitude_deg'],
                );

                $this->model->user->updateById($user['user_id'], $coordinates);

                $done++;
            } else {
                $ville = $this->model->city->find(array('ville_longitude_deg', 'ville_latitude_deg'), array('%ville_nom_simple' => $user['user_city']), array(), '0, 1');

                if (!empty($ville[0])) {
                    $coordinates = array(
                        'user_longitude' => $ville[0]['ville_longitude_deg'],
                        'user_latitude' => $ville[0]['ville_latitude_deg'],
                    );

                    $this->model->user->updateById($user['user_id'], $coordinates);

                    $done++;
                }
            }
        }

        $this->view->growler($done . ' utilisateur migrés', GROWLER_OK);

        $this->render();
    }

    public function renderScriptRemoveUnsedLinks()
    {
        $rawUserIds = $this->model->user->find(array('user_id'));

        $userIds = array();
        foreach ($rawUserIds as $userIdrow) {
            $userIds[] = $userIdrow['user_id'];
        }

        $links = $this->model->link->find();
        $linksToRemove = array();

        $deleteSql = '';

        foreach ($links as $link) {
            if (!in_array($link['destinataire_id'], $userIds)) {
                $deleteSql .= 'DELETE FROM link WHERE destinataire_id = ' . $link['destinataire_id'] . '; ';
            }

            if (!in_array($link['expediteur_id'], $userIds)) {
                $deleteSql .= 'DELETE FROM link WHERE expediteur_id = ' . $link['expediteur_id'] . '; ';
            }
        }

        if ($this->model->execute($deleteSql)) {
            $this->view->growler('links inutiles supprimés.', GROWLER_OK);
        } else {
            $this->view->growlerError();
        }

        $this->render();

    }

    public function renderScriptMigrateUserData()
    {
        $serializedValues = array(
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

        $sql = 'SELECT user_id,
                    user_profession,
                    user_poids,
                    user_taille,
                    user_tattoo,
                    user_piercing,
                    look_id,
                    user_smoke,
                    user_alcohol,
                    user_drugs
                FROM user;
        ;';

        $users = Db::executeStmt(Db::getInstance()->prepare($sql))->fetchAll();

        $rows = 0;
        foreach ($users as $user) {
            $sql = 'UPDATE user SET user_data = :user_data WHERE user_id = :user_id;';

            $stmt = Db::getInstance()->prepare($sql);

            $serializedData = array();
            foreach ($serializedValues as $key) {
                $serializedData[$key] = $user[$key];
            }

            $stmt->bindValue('user_id', $user['user_id']);
            $stmt->bindValue('user_data', serialize($serializedData));

            if (Db::executeStmt($stmt)) {
                $rows++;
            }
        }

        $sql = 'ALTER TABLE `user` DROP `eyes_id`, DROP `origin_id`, DROP `user_alcohol`, DROP `user_smoke`, DROP `user_tattoo`, DROP `user_drugs`, DROP `hair_id`, DROP `user_piercing`, DROP `user_profession`, DROP `user_music`, DROP `user_poids`, DROP `user_taille`, DROP `week_user`;';

        if (Db::executeStmt(Db::getInstance()->prepare($sql))) {
            $this->view->growler($rows . ' utilisateur migrés', GROWLER_OK);
        }

        $this->render();
    }

    public function renderScriptLinkAllTritt()
    {
        $bunchNumber = 20;
        $userCount = $this->model->user->count(array('!user_login' => 'Tritt'));
        $max = round($userCount/$bunchNumber, 0);

        $sql = '
            DELETE FROM
                link
            WHERE
                expediteur_id = :user_id
            OR
                destinataire_id = :user_id;
        ';

        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('user_id', $this->context->get('user_id'));
        $stmt->execute();

        for ($i=1; $i<$max; $i++) {
            $bunchOffset = (($i - 1) * $bunchNumber).','.($i * $bunchNumber);
            $userBunch = $this->model->user->find(
                array('user_id'),
                array('!user_login' => 'Tritt'),
                array(),
                $bunchOffset
            );

            $sql = '';
            foreach ($userBunch as $row) {
                $sql .= '
                    REPLACE INTO
                        link
                    (
                        expediteur_id,
                        destinataire_id,
                        status,
                        modification_date
                    )
                    VALUES (
                        ' . $this->context->get('user_id') . ',
                        ' . $row['user_id'] . ',
                        ' . LINK_STATUS_ACCEPTED . ',
                        NOW()
                    )
                ;';
            }

            $this->model->execute($sql);
        }

        $this->view->growler('Opération effectuée', GROWLER_OK);
        $this->render();
    }

    public function renderScriptPwdToMd5()
    {
        $users = $this->model->execute("UPDATE user SET user_pwd = md5(user_pwd)");
        $this->view->growler('Le script a été executé avec succès', GROWLER_OK);
        $this->render();
    }

    public function renderScriptMigrateTastes()
    {
        $reference = array('band' => 'groupes',
                         'passion' => 'passions',
                         'book' => 'livres');

        $types = array('band', 'book', 'passion');
        $error = 0;
        $i     = 0;

        $users = $this->model->fetch("SELECT distinct list_id, user_id, user_music FROM user");
        foreach ($users as $user) {
            $newData     = false;
            $tastesDatas = array();
            if (!empty($user['user_music'])) {
                $musicDatas = str_replace('/', ',', $user['user_music']);
                $musicDatas = explode(',', $musicDatas);
                foreach ($musicDatas as $musicData) {
                    $newData = true;
                    $tastesDatas['instruments'][] = trim($musicData);
                }
            }
            foreach ($types as $type) {
                $sql = "SELECT ".$type."_libel
                  FROM list_".$type.", ref_".$type."
                  WHERE list_".$type.".".$type."_id = ref_".$type.".".$type."_id
                  AND list_id = ".$user['list_id'];
                $tastes = $this->model->fetch($sql);
                $tmpTaste = array();
                foreach ($tastes as $taste) {
                    $newData = true;
                    $tmpTaste[] = addslashes($taste[$type."_libel"]);
                }
                $tastesDatas[$reference[$type]] = $tmpTaste;
            }
            if ($newData) {
                $sql = "INSERT IGNORE INTO taste (user_id, data)
                  VALUES ('" . $user['user_id'] . "','" . serialize($tastesDatas) . "');";
                if ($this->model->execute($sql)) {
                    $i++;
                } else {
                    $error++;
                }
            }
        }
        $this->view->growler('Le script a été executé avec '.$i.' valeurs modifiés', GROWLER_INFO);
        $this->render();
    }

    public function renderScriptDeleteUnusedPhotos()
    {
        $return = $this->model->load('photo')->deleteUnusedPhotos();
        if ($return) {
            $this->view->growler('Le script a été executé', GROWLER_OK);
        } else {
            $this->view->growlerError();
        }
        $this->render();
    }

    public function renderScriptDeleteTempDatas()
    {
        $return = $this->model->load('util')->deleteTempDatas();
        if ($return) {
            $this->view->growler('Le script a été executé', GROWLER_OK);
        } else {
            $this->view->growlerError();
        }
        $this->render();
    }

    public function renderScriptDeleteUnusedAccounts()
    {
        $return = $this->model->load('admin')->deleteUnusedAccounts();
        if ($return) {
            $this->view->growler('Le script a été executé', GROWLER_OK);
        } else {
            $this->view->growlerError();
        }
        $this->render();
    }


    public function renderScriptMigrateLink()
    {
        $error = 0;
        $i     = 0;
         $messages = $this->model->fetch('SELECT * FROM tmp_message WHERE state_id IN (6, 7, 8)');
        foreach ($messages as $message) {
            try {
                $this->model->execute("REPLACE INTO link (expediteur_id, destinataire_id, status)
                                            VALUES ('".$message['expediteur_id']."', '".$message['destinataire_id']."', '".$message['state_id']."');");
                $i++;
            } catch (Exception $e) {
                $error++;
            }

        }
         echo $i.' valeurs modifiés ';
         echo $error.' erreurs ';
         $this->view->growler('Le script a été executé avec '.$i.' valeurs modifiés', GROWLER_INFO);
         $this->render();
    }
}
