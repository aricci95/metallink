<?php

class Views extends AppModel
{

    public function addView($viewedId)
    {
        $checkSQL = "DELETE FROM user_views 
                    WHERE viewer_id = '" . $this->getContextUser('id') . " ' 
                    AND   viewed_id = '".$this->securize($viewedId)."';";
        $this->execute($checkSQL);
        $sql = "INSERT INTO user_views (viewer_id, viewed_id, view_date) VALUES ('" . $this->getContextUser('id') . "', '".$this->securize($viewedId)."', NOW());";
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
    			FROM user_views JOIN user ON (user.user_id = user_views.viewer_id)                        
    			WHERE viewed_id = '".$this->securize($this->getContextUser('id'))."'
                ORDER BY view_date DESC
                LIMIT ".($offset * NB_SEARCH_RESULTS).", ".NB_SEARCH_RESULTS.";";
        return $this->fetch($sql);
    }

    // Supprime les vues d'un utilisateur
    public function deleteViewsById($id)
    {
        $sql               = "DELETE FROM user_views WHERE viewed_id = '$id';";
        $resultat          = $this->execute($sql, true);
        $_SESSION['views'] = 0;
        return $resultat;
    }
}
