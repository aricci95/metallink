<?php

class Views extends AppModel
{

    public function addView($viewedId)
    {
        $checkSQL = "DELETE FROM user_views
                    WHERE viewer_id = :context_user_id
                    AND   viewed_id = :viewed_id";

        $stmt = Db::getInstance()->prepare($checkSQL);

        $stmt->bindValue(':context_user_id', $this->context->get('user_id'), PDO::PARAM_INT);
        $stmt->bindValue(':viewed_id', $viewedId, PDO::PARAM_INT;

        Db::executeStmt($stmt);

        $sql = "INSERT INTO user_views (
                    viewer_id,
                    viewed_id,
                    view_date
                ) VALUES (
                    :context_user_id,
                    :viewed_id,
                    NOW()
                );";

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue(':context_user_id', $this->context->get('user_id'), PDO::PARAM_INT);
        $stmt->bindValue(':viewed_id', $viewedId, PDO::PARAM_INT;

        return Db::executeStmt($stmt);
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
			WHERE viewed_id = :context_user_id
            ORDER BY view_date DESC
            LIMIT :limit_begin, :limit_end;
        ";

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue(':context_user_id', $this->context->get('user_id'), PDO::PARAM_INT);
        $stmt->bindValue(':limit_begin', $offset * NB_SEARCH_RESULTS, PDO::PARAM_INT);
        $stmt->bindValue(':limit_end', NB_SEARCH_RESULTS, PDO::PARAM_INT);

        return Db::executeStmt($stmt)->fetch($sql);
    }

    // Supprime les vues d'un utilisateur
    public function deleteViewsById($id)
    {
        $sql      = "DELETE FROM user_views WHERE viewed_id = '$id';";
        $resultat = $this->execute($sql, true);

        $this->context->set('views', 0);

        return $resultat;
    }
}
