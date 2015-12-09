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
        $this->_view->scripts = $scripts;
        $this->_view->setViewName('admin/wScript');
        $this->_view->setTitle('Scripts');
        $this->_view->render();
    }

    public function renderScriptPwdToMd5()
    {
        $users = $this->_model->execute("UPDATE user SET user_pwd = md5(user_pwd)");
        $this->_view->growler('Le script a été executé avec succès', GROWLER_OK);
        $this->render();
    }

    public function renderScriptMigrateTastes()
    {
        $reference = array('band' => 'groupes',
                         'passion' => 'passions',
                         'book' => 'livres');
                     //    'music' => 'instruments');
        $types = array('band', 'book', 'passion');
        $error = 0;
        $i     = 0;

        $users = $this->_model->fetch("SELECT distinct list_id, user_id, user_music FROM user");
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
                $tastes = $this->_model->fetch($sql);
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
                if ($this->_model->execute($sql)) {
                    $i++;
                } else {
                    $error++;
                }
            }
        }
        $this->_view->growler('Le script a été executé avec '.$i.' valeurs modifiés', GROWLER_INFO);
        $this->render();
    }

    public function renderScriptDeleteUnusedPhotos()
    {
        $return = $this->_model->load('photo')->deleteUnusedPhotos();
        if ($return) {
            $this->_view->growler('Le script a été executé', GROWLER_OK);
        } else {
            $this->_view->growlerError();
        }
        $this->render();
    }

    public function renderScriptDeleteTempDatas()
    {
        $return = $this->_model->load('util')->deleteTempDatas();
        if ($return) {
            $this->_view->growler('Le script a été executé', GROWLER_OK);
        } else {
            $this->_view->growlerError();
        }
        $this->render();
    }

    public function renderScriptDeleteUnusedAccounts()
    {
        $return = $this->_model->load('admin')->deleteUnusedAccounts();
        if ($return) {
            $this->_view->growler('Le script a été executé', GROWLER_OK);
        } else {
            $this->_view->growlerError();
        }
        $this->render();
    }


    public function renderScriptMigrateLink()
    {
        $error = 0;
        $i     = 0;
         $mails = $this->_model->fetch('SELECT * FROM tmp_mail WHERE mail_state_id IN (6, 7, 8)');
        foreach ($mails as $mail) {
            try {
                $this->_model->execute("REPLACE INTO link (expediteur_id, destinataire_id, status) 
                                            VALUES ('".$mail['mail_expediteur']."', '".$mail['mail_destinataire']."', '".$mail['mail_state_id']."');");
                $i++;
            } catch (Exception $e) {
                $error++;
            }
             
        }
         echo $i.' valeurs modifiés ';
         echo $error.' erreurs ';
         $this->_view->growler('Le script a été executé avec '.$i.' valeurs modifiés', GROWLER_INFO);
         $this->render();
    }
}
