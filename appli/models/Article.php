<?php

/*
 *  Classe d'accès aux données des articles
 */
class Article extends AppModel
{

    public function getSearch($criterias, $offset = 0)
    {
        $sql = 'SELECT
                    art_id,
                    art_libel,
                    art_date,
                    ref_state_id,
                    categorie_id,
                    art_price,
                    art_photo_url,
                    user_city
                FROM article, user
                WHERE ';
        if (!empty($criterias['search_libel'])) {
            $sql .= "art_libel LIKE '".$criterias['search_libel']."%' AND ";
        }
        if (!empty($criterias['search_categorie'])) {
            $sql .= "categorie_id = '".$criterias['search_categorie']."' AND ";
        }
        $sql .= 'art_state_id = '.ARTICLE_STATE_ON_SALE.'
        AND article.user_id = user.user_id
        ORDER BY art_date DESC
        LIMIT '.($offset * NB_SEARCH_RESULTS).', '.NB_SEARCH_RESULTS.';';
        return $this->fetch($sql);
    }

    public function getNew()
    {
        $sql = '
            SELECT
                art_id,
                art_libel,
                art_date,
                ref_state_id,
                categorie_id,
                art_price,
                art_photo_url
            FROM article
            ORDER BY art_date DESC
            LIMIT 0, 3
        ;';

        return $this->fetch($sql);
    }
        // Récupère un article
    public function getById($artId)
    {
        $sql = "SELECT art_id,
                        art_libel,
                        UNIX_TIMESTAMP(art_date) as art_date,
                        ref_state_id,
                        art_description,
                        article.categorie_id,
                        UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                        art_price,
                        art_photo_url,
                        livre_surplace,
                        livre_poste,
                        categorie_libel,
                        article.user_id as user_id,
                        user_login,
                        UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                        user_gender,
                        user_photo_url,
                        (YEAR(CURRENT_DATE)-YEAR(user_birth)) - (RIGHT(CURRENT_DATE,5)<RIGHT(user_birth,5)) AS age,
                        user_city
                FROM article
                LEFT JOIN ref_categorie ON article.categorie_id = ref_categorie.categorie_id
                LEFT JOIN user ON article.user_id = user.user_id
                WHERE art_id = '$artId';";
        return $this->fetchOnly($sql);
    }

    // Récupère les categories
    public function getCategories()
    {
        $sql = "SELECT main_categorie_id as id,
                       main_categorie_libel as libel
                FROM ref_main_categorie
                ORDER BY main_categorie_libel;";
        return $this->fetch($sql);
    }

    public function deleteArticleById($id)
    {
        $this->load('Photo')->deletePhotosById($id, PHOTO_TYPE_ARTICLE);
        return $this->execute("DELETE FROM article WHERE art_id = ".$this->securize($id));
    }

    public function updateArticle($datas)
    {
        if (!empty($datas['art_id'])) {
            $sql = 'UPDATE article SET '
                .' art_libel = \''.$datas['art_libel'].'\', '
                .' art_price = '.$datas['art_price'].', '
                .' categorie_id = '.$datas['categorie_id'].', '
                .' art_description = \''.$datas['art_description'].'\', '
                .' livre_poste = '.$datas['livre_poste'].', '
                .' livre_surplace = '.$datas['livre_surplace']
                .' WHERE art_id = '.$this->securize($datas['art_id']);
        }
        return $this->execute($sql);
    }

    public function createArticle($items)
    {
        $sql = 'INSERT INTO article (art_libel,
                                    categorie_id,
                                    art_description,
                                    art_state_id,
                                    art_price,
                                    art_date,
                                    user_id,
                                    livre_poste,
                                    livre_surplace)
                VALUES ("'.$items['art_libel'].'",
                         '.$items['categorie_id'].',
                         "'.$items['art_description'].'",
                         '.ARTICLE_STATE_ON_SALE.',
                         '.$items['art_price'].',
                         NOW(),
                         '.$this->getContextUser('id').',
                         '.$items['livre_poste'].',
                         '.$items['livre_surplace'].');';
        if ($this->execute($sql)) {
            return $this->insertId();
        } else {
            return false;
        }
    }
}
