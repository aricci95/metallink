<?php

/* 
 *  Classe d'accès aux données des utilisateurs
 */
class News extends AppModel
{
    
    // Récupère la liste des news
    public function getNews()
    {
        $sql = "SELECT 
                    news.news_id as news_id,
                    news_contenu,
                    news_date,
                    news_titre,
                    news.user_id,
                    user_login,
                    news_type_id,
                    news_media_url,
                    news_photo_url
                FROM news, user
                WHERE news.user_id = user.user_id
                ORDER BY news_date DESC
                LIMIT 0, 6";
        return $this->fetch($sql);
    }
    
    // Récupère une news
    public function getNewsById($newsId)
    {
        $sql = "SELECT 
                    news.news_id as news_id,
                    news_type_id,
                    news_contenu,
                    news_date,
                    user_login,
                    news_titre,
                    news.user_id as news_auteur_id,
                    news_photo_url,
                    news_media_url
                FROM user, news
                WHERE news.user_id = user.user_id
                AND news.news_id = '".$newsId."'
                ORDER BY news_date;";

        $resultat = $this->fetchOnly($sql);
        return $resultat;
    }
    
    // Modifie une news
    public function updateNewsById($newContent)
    {
        if (!empty($newContent['news_id'])) {
            $sql = "UPDATE news SET news_contenu = '".$newContent['news_contenu']."',
                                news_titre = '".$newContent['news_titre']."',
                                news_media_url = '".$newContent['news_media_url']."',
                                news_photo_url = '".$newContent['news_photo_url']."',
                                news_date = '".date("Y-m-d H:i")."'
                                WHERE news_id = '".$newContent['news_id']."';";
            return $this->execute($sql);
        } else {
            return false;
        }
    }
    
    // Supprime une news
    public function deleteNewsById($newsId)
    {
        return $this->execute("DELETE FROM news WHERE news_id = '$newsId'");
    }
    
    // Ajoute une news
    public function addNews($news)
    {
        $sql = "INSERT INTO news (news_titre, news_contenu, news_date, news_type_id, user_id, news_media_url, news_photo_url) 
                VALUES ('"
                    .$news['news_titre']."', '"
                    .addslashes($news['news_contenu'])."', '"
                    .date("Y-m-d H:i")."', '1', '"
                    .$_SESSION['user_id']."', '"
                    .$news['news_media_url']."', '"
                    .$news['news_photo_url']."');";
        return  $this->execute($sql);
    }
}
