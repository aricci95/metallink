<?php

class PhotoService
{

    public function delete($id, $path)
    {
        if (file_exists(ROOT_DIR . '/photos/small/' . $path)) {
            unlink(ROOT_DIR . '/photos/small/' . $path);
        }

        if (file_exists(ROOT_DIR . '/photos/profile/' . $path)) {
            unlink(ROOT_DIR . '/photos/profile/' . $path);
        }

        Photo::deleteById($id);

        return true;
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
    public function uploadImage($photoFiles)
    {
        $errors = 0;
        $image        = $photoFiles["name"];
        $uploadedfile = $photoFiles['tmp_name'];

        if ($image) {
            $filename = stripslashes($photoFiles['name']);
            $extension = $this->_getExtension($filename);
            $extension = strtolower($extension);

            if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) {
                throw new Exception('Type d\'image  inconnu');
            } else {
                $size = filesize($photoFiles['tmp_name']);

                if ($size > MAX_SIZE * 1024) {
                    throw new Exception('Votre image est trop lourde');
                }

                list($width, $height) = getimagesize($uploadedfile);

                if ($width > MAX_DIMENSION || $height > MAX_DIMENSION) {
                    throw new Exception('Votre image est trop grande <br/> (dimensions max : ' . MAX_DIMENSION . ' x ' . MAX_DIMENSION . ')');
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
                $photo['type_id']   = (int) $photoFiles['type_id'];
                $photo['key_id']    = (int) $photoFiles['key_id'];

                $photo['photo_id']  = Photo::insert($photo);

                $filename  = ROOT_DIR . '/photos/profile/' . $photo['photo_url'];
                $filename1 = ROOT_DIR . '/photos/small/' . $photo['photo_url'];

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
