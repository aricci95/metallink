<?php

class Tools
{

    public static function timeConvert($time)
    {
        $timeNow = time();
        $time = $timeNow - $time;
        if ($time <= 0) {
            echo 'maintenant';
        } elseif ($time < 60) echo 'il y a '.$time.' secondes.';
        elseif ($time >= 60 && $time < 3600) {
            $time = round($time / 60);
            echo 'il y a '.$time.' minute';
            if ($time > 1) {
                echo 's';
            }
        } elseif ($time >= 3600 && $time < 86400) {
            $time = round($time / 3600);
            echo 'il y a '.$time.' heure';
            if ($time > 1) {
                echo 's';
            }
        } elseif ($time >= 86400 && $time < 604800) {
            $time = round($time / 86400);
            echo 'il y a '.$time.' jour';
            if ($time > 1) {
                echo 's';
            }
        } elseif ($time >= 604800 && $time < 2592000) {
            $time = round($time / 604800);
            echo 'il y a '.$time.' semaine';
            if ($time > 1) {
                echo 's';
            }
        } elseif ($time >= 2592000) {
            $time = round($time / 2592000);
            echo 'il y a '.$time.' mois.';
        }
    }

    public static function no_special_character($chaine)
    {

        //  les accents
        $chaine=trim($chaine);
        $chaine= strtr($chaine, "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ", "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");

        //  les caracètres spéciaux (aures que lettres et chiffres en fait)
        $chaine = preg_replace('/([^.a-z0-9]+)/i', '', $chaine);

        return $chaine;
    }

    public static function check_special_character($chaine)
    {

         //  les accents
        $chaine=trim($chaine);
        $clean = str_replace("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ", "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn", $chaine);
        if ($clean != $chaine) {
            return false;
        } else {
                //  les caracètres spéciaux (aures que lettres et chiffres en fait)
            $clean = preg_replace('/([^.a-z0-9]+)/i', '', $chaine);
        }
        if ($clean != $chaine) {
            return false;
        }
        return true;
    }

    // Convertis une date d'anglais vers français
    public static function toFrenchDate($englishDate, $details = true)
    {
        if ($details == true) {
            $frenchDate = date("d/m/Y H:i", strtotime($englishDate));
        } else {
            $frenchDate = date("d/m/Y", strtotime($englishDate));
        }
        return $frenchDate;
    }


    // Convertis les symboles en smileys
    public static function toSmiles($texte)
    {
        $smiles = array(" :-)" => " <img src='MLink/images/smilies/content.png' />",
                        " :)" => " <img src='MLink/images/smilies/content.png' />",
                        " =)" => " <img src='MLink/images/smilies/content.png' />",
                        " :p" => " <img src='MLink/images/smilies/langue.png' />",
                        " :-P" => " <img src='MLink/images/smilies/langue.png' />",
                        " :P" => " <img src='MLink/images/smilies/langue.png' />",
                        " =P" => " <img src='MLink/images/smilies/langue.png' />",
                        " :'(" => " <img src='MLink/images/smilies/triste.png' />",
                        " :-'(" => " <img src='MLink/images/smilies/triste.png' />",
                        " XD" => " <img src='MLink/images/smilies/XD.png' />",
                        " xD" => " <img src='MLink/images/smilies/XD.png' />",
                        " =/" => " <img src='MLink/images/smilies/septique.png' />",
                        " :/" => " <img src='MLink/images/smilies/septique.png' />",
                        " :-/" => " <img src='MLink/images/smilies/septique.png' />",
                        " T.T" => " <img src='MLink/images/smilies/trestriste.png' />",
                        " <3" => " <img src='MLink/images/smilies/love.png' />",
                        " >.<" => " <img src='MLink/images/smilies/vener.png' />",
                        " ;)" => " <img src='MLink/images/smilies/sourcils.png' />",
                        " ;-)" => " <img src='MLink/images/smilies/sourcils.png' />",
                        " ^^" => " <img src='MLink/images/smilies/sourcils.png' />",
                        " ^.^" => " <img src='MLink/images/smilies/sourcils.png' />");
        return strtr($texte, $smiles);
    }

    public static function getCleanName($string)
    {

        return strtolower(trim(str_replace(array(" ", "'", "-", ",", ";", ".", "+", "(", ")"), array(), self::no_special_character($string))));
    }
}
