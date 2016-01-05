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

    public function renderScriptLinkAllTritt()
    {
        $bunchNumber = 20;
        $userCount = User::count(array('!user_login' => 'Tritt'));
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
        $stmt->bindValue('user_id', User::getContextUser('id'));
        $stmt->execute();

        for ($i=1; $i<$max; $i++) {
            $bunchOffset = (($i - 1) * $bunchNumber).','.($i * $bunchNumber);
            $userBunch = User::find(
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
                        ' . User::getContextUser('id') . ',
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
         $messages = $this->model->fetch('SELECT * FROM tmp_mail WHERE state_id IN (6, 7, 8)');
        foreach ($messages as $message) {
            try {
                $this->model->execute("REPLACE INTO link (expediteur_id, destinataire_id, status)
                                            VALUES ('".$message['expediteur']."', '".$message['destinataire']."', '".$message['state_id']."');");
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
