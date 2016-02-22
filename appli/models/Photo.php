<?php

/*
 *  Classe d'accès aux données des utilisateurs
 */
class Photo extends AppModel
{

    public function getPhotosByKey($photoKey, $photoType)
    {
        $sql = '
            SELECT *
            FROM photo
            WHERE key_id = :photo_key
            AND type_id = :type_id
            ORDER BY photo_id DESC
        ;';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':photo_key', $photoKey);
        $stmt->bindValue(':type_id', $photoType);
        $stmt->execute();

        return $stmt->fetchAll();
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

            $sql = 'DELETE FROM photo WHERE photo_id = :photo_id';

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue('photo_id', $photo['photo_id']);

            $stmt->execute();
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
            echo 'suppression de '.$photo['photo_url'].'<br>';
            $this->deletePhotoById($photo['photo_id'], $photo['photo_url']);
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
}
