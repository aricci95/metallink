<?php

class Views extends AppModel
{

    public function addView($viewedId)
    {
        $checkSQL = "DELETE FROM userviews
                    WHERE viewer_id = '" . User::getContextUser('id') . " '
                    AND   viewed_id = '".$this->securize($viewedId)."';";
        $this->execute($checkSQL);
        $sql = "INSERT INTO userviews (viewer_id, viewed_id, view_date) VALUES ('" . User::getContextUser('id') . "', '".$this->securize($viewedId)."', NOW());";
        return $this->execute($sql);
    }

    // Récupère la liste des vues d'un utilisateur
    public function getUserViews($offset = 0)
    {
        $sql = "SELECT viewer_id,
    				    user_gender,
        				user_id,
        				user_photo_url,
        				user_login,
                        user_mail,
                        UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                        FLOOR((DATEDIFF( CURDATE(), (user_birth))/365)) AS age
    			FROM userviews JOIN user ON (user.user_id = userviews.viewer_id)
    			WHERE viewed_id = '".$this->securize(User::getContextUser('id'))."'
                ORDER BY view_date DESC
                LIMIT ".($offset * NB_SEARCH_RESULTS).", ".NB_SEARCH_RESULTS.";";
        return $this->fetch($sql);
    }

    // Supprime les vues d'un utilisateur
    public function deleteViewsById($id)
    {
        $sql               = "DELETE FROM userviews WHERE viewed_id = '$id';";
        $resultat          = $this->execute($sql, true);
        $_SESSION['views'] = 0;
        return $resultat;
    }
}
