<?php

/*
 *  Classe d'accès aux données des utilisateurs
 */
class Photo extends AppModel
{

    // Récupère une photo par cle
    public function getPhotosByKey($photoKey, $photoType)
    {
        $sql = "SELECT *
                FROM photo
                WHERE key_id = $photoKey
                AND type_id = $photoType
                ORDER BY photo_id DESC;";
        return $this->fetch($sql);
    }

    // Ajoute une photo
    public function addPhoto($photo)
    {
        $sql = "INSERT INTO photo (photo_id, key_id, photo_url, type_id) VALUES ('', '" . $photo['key_id'] . "','" . $photo['photo_url'] . "', '".$photo['type_id']."')";
        $this->execute($sql);
        return $this->insertId();
    }

    public function setProfilePhoto($photo)
    {
        if ($photo['type_id'] == PHOTO_TYPE_USER) {
            $sql = "UPDATE user SET user_photo_url = '".$photo['photo_url']."' WHERE user_id = '".$photo['key_id']."';";
        } else {
            $sql = "UPDATE article SET art_photo_url = '".$photo['photo_url']."' WHERE art_id = '".$photo['key_id']."';";
        }
        $this->execute($sql);
    }

    // Supprime une photo
    public function deletePhoto($photo)
    {
        if (file_exists(ROOT_DIR.'/photos/small/'.$photo['photo_url'])) {
            unlink(ROOT_DIR.'/photos/small/'.$photo['photo_url']);
        }
        if (file_exists(ROOT_DIR.'/photos/profile/'.$photo['photo_url'])) {
            unlink(ROOT_DIR.'/photos/profile/'.$photo['photo_url']);
        }
        $sql = "DELETE FROM photo WHERE photo_id = '" . $photo['photo_id'] . "'";
        return $this->execute($sql);
    }

    // Supprime une photo
    public function deletePhotosById($keyId, $typeId)
    {
        $photos = $this->getPhotosByKey($keyId, $typeId);
        foreach ($photos as $photo) {
            if (file_exists(ROOT_DIR.'/photos/small/'.$photo['photo_url'])) {
                unlink(ROOT_DIR.'/photos/small/'.$photo['photo_url']);
            }
            if (file_exists(ROOT_DIR.'/photos/profile/'.$photo['photo_url'])) {
                unlink(ROOT_DIR.'/photos/profile/'.$photo['photo_url']);
            }
            $this->execute("DELETE FROM photo WHERE photo_id = '" . $photo['photo_id'] . "'");
        }
        return true;
    }

    // Supprime les photos inutilisées
    public function deleteUnusedPhotos()
    {
            $count = 0;
            // Recherche des photos secondaires d'utilisateur supprimés
            $selectSql = "SELECT photo_id, photo_url
                           FROM photo
                           WHERE key_id NOT IN (SELECT user_id FROM user)";
            $photos = $this->fetch($selectSql);

        foreach ($photos as $key => $photo) {
            echo 'suprresion de '.$photo['photo_url'].'<br>';
            self::deletePhotoById($photo['photo_id'], $photo['photo_url']);
            $count++;
        }

            // on récupère toutes les références de photos
            $selectSql = "(SELECT photo_url, photo_id FROM photo)
                            UNION
                            (SELECT user_photo_url as photo_url, 'none' as photo_id FROM user)";
            $photosBdd = $this->fetch($selectSql);

        foreach ($photosBdd as $key => $value) {
            $photoBdd[$value['photo_url']] = $value['photo_id'];
        }

            // Recherche des photos qui ne sont pas en BDD
            $profileDirname = ROOT_DIR.'/photos/profile/';
            $smallDirname = ROOT_DIR.'/photos/small/';
            $profileDir = opendir($profileDirname);
            $smallDir = opendir($smallDirname);

            $photosDelete = array();
        while ($file = readdir($profileDir)) {
            if ($file != '.' && $file != '..' && !is_dir($profileDirname.$file)) {
                if (!array_key_exists($file, $photoBdd) && strtoupper($file) != 'UNKNOWUSER.JPG') {
                    if (file_exists(ROOT_DIR.'/photos/profile/'.$file)) {
                        unlink(ROOT_DIR.'/photos/profile/'.$file);
                        echo $file.' supprimé<br>';
                        $count++;
                    }
                }
            }
        }
        while ($file = readdir($smallDir)) {
            if ($file != '.' && $file != '..' && !is_dir($smallDirname.$file)) {
                if (!array_key_exists($file, $photoBdd) && strtoupper($file) != 'UNKNOWUSER.JPG') {
                    if (file_exists(ROOT_DIR.'/photos/small/'.$file)) {
                        unlink(ROOT_DIR.'/photos/small/'.$file);
                        echo $file.' supprimé<br>';
                        $count++;
                    }
                }

            }
        }
            echo "<b>$count photos supprimées</b>.";
            // Fermeture des répertoires
            closedir($profileDir);
            closedir($smallDir);
    }

    // Récupère l'extension
    private function _getExtension($str)
    {
        $i = strrpos($str, ".");
        if (! $i) {
            return "";
        }

        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }

    // Upload image
    public function uploadImage($photoFiles, $view)
    {
        $errors = 0;
        $image        = $photoFiles["name"];
        $uploadedfile = $photoFiles['tmp_name'];

        if ($image) {
            $filename = stripslashes($photoFiles['name']);
            $extension = $this->_getExtension($filename);
            $extension = strtolower($extension);
            if (($extension != "jpg") && ($extension != "jpeg") &&

            ($extension != "png") && ($extension != "gif")) {
                $view->growler('Type d\'image  inconnu', GROWLER_ERR);
                return false;
            } else {
                $size = filesize($photoFiles['tmp_name']);

                if ($size > MAX_SIZE * 1024) {
                    $view->growler('Votre image est trop lourde', GROWLER_ERR);
                    return false;
                }
                list($width, $height) = getimagesize($uploadedfile);
                if ($width > MAX_DIMENSION || $height > MAX_DIMENSION) {
                    $view->growler("Votre image est trop grande <br> (dimensions max : ".MAX_DIMENSION." x ".MAX_DIMENSION.")", GROWLER_ERR);
                    return false;
                }
                if ($extension == "jpg" || $extension == "jpeg") {
                    $uploadedfile = $photoFiles['tmp_name'];
                    $src = imagecreatefromjpeg($uploadedfile);
                } else if ($extension == "png") {
                    $uploadedfile = $photoFiles['tmp_name'];
                    $src = imagecreatefrompng($uploadedfile);
                } else {
                    $src = imagecreatefromgif($uploadedfile);
                }
                // PROFILE (qualibrage de la largeur)
                $profileTmp = null;
                $profilewidth = $width;
                $profileheight = $height;
                $profileTmp = imagecreatetruecolor($profilewidth, $profileheight);
                imagecopyresampled($profileTmp, $src, 0, 0, 0, 0, $profilewidth, $profileheight, $width, $height);

                // ICONE (qualibrage de la hauteur)
                $smallTmp = null;
                $smallheight = 150;
                $smallwidth = ($width / $height) * $smallheight;
                $smallTmp = imagecreatetruecolor($smallwidth, $smallheight);
                imagecopyresampled($smallTmp, $src, 0, 0, 0, 0, $smallwidth, $smallheight, $width, $height);

                $photo['photo_url'] = uniqid().'.'. $extension;
                $photo['type_id']   = $photoFiles['type_id'];
                $photo['key_id']    = $photoFiles['key_id'];
                $photo['photo_id']  = $this->addPhoto($photo);
                $filename  = ROOT_DIR.'/photos/profile/'.$photo['photo_url'];
                $filename1 = ROOT_DIR.'/photos/small/'.$photo['photo_url'];


                // Creation
                imagejpeg($profileTmp, $filename, 100);
                imagejpeg($smallTmp, $filename1, 100);

                // DESTRUCTION
                imagedestroy($src);
                imagedestroy($smallTmp);
                imagedestroy($profileTmp);

                return $photo;
            }
        }
    }
}
