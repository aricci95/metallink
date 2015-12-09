<?php

/* 
 *  Classe d'accès aux données des covoiturages
 */
class Covoit extends AppModel
{
      
    public function getSearch($criterias, $offset = 0)
    {
        $sql = 'SELECT
                    covoit_id,
                    concert.concert_id as concert_id,
                    DATE_FORMAT(date_depart,\'%d/%m %H:%i\') as date_depart,
                    DATE_FORMAT(date_retour,\'%d/%m %H:%i\') as date_retour,
                    user.user_id as user_id,
                    UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                    price,
                    ville.ville_id as ville_id,
                    nom,
                    concert_libel,
                    user_login,
                    user_gender,
                    user_photo_url
                FROM covoit JOIN concert ON (concert.concert_id = covoit.concert_id)
                            JOIN user ON (covoit.user_id = user.user_id)
                            JOIN ville ON (ville.ville_id = covoit.ville_id) 
                WHERE ';
        if (!empty($criterias['search_concert'])) {
            $sql .= "concert.concert_id LIKE '".$criterias['search_concert']."%' AND ";
        }
        if (!empty($criterias['search_ville'])) {
            $sql .= "ville.ville_id = '".$criterias['search_ville']."' AND ";
        }
        $sql .= ' 1=1 ORDER BY date_depart DESC LIMIT '.($offset * NB_SEARCH_RESULTS).', '.NB_SEARCH_RESULTS.';';
        return $this->fetch($sql);
    }

    public function getNew()
    {
        $sql = 'SELECT
                    covoit_id,
                    concert.concert_id as concert_id,
                    DATE_FORMAT(date_depart,\'%d/%m %H:%i\') as date_depart,
                    DATE_FORMAT(date_retour,\'%d/%m %H:%i\') as date_retour,
                    user.user_id as user_id,
                    price,
                    UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                    ville.ville_id as ville_id,
                    nom,
                    concert_libel,
                    user_login,
                    user_gender,
                    user_photo_url
                FROM covoit JOIN concert ON (concert.concert_id = covoit.concert_id)
                            JOIN user ON (covoit.user_id = user.user_id)
                            JOIN ville ON (ville.ville_id = covoit.ville_id)
                LIMIT 0, 1;';
                return $this->fetch($sql);
    }

    public function getById($id)
    {
        $sql = "SELECT covoit_id,
                    concert_id,
                    date_depart, 
                    date_retour,
                    user_id,
                    UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                    price,
                    ville.ville_id as ville_id,
                    concert_libel
                FROM covoit JOIN concert ON (concert.concert_id = covoit.concert_id)
                            JOIN user ON (covoit.user_id = user.user_id)
                            JOIN ville ON (ville.ville_id = covoit.ville_id)
                WHERE covoit_id = '$id';";
        return $this->fetchOnly($sql);
    }
    
    public function deleteById($id)
    {

        return $this->execute("DELETE FROM covoit WHERE covoit_id = ".$this->securize($id));
    }
    
    public function update($datas)
    {
        if (!empty($datas['covoit_id'])) {
            $sql = 'UPDATE covoit SET '
                .' concert_id = \''.$datas['concert_id'].'\', '
                .' ville_id = '.$datas['ville_id'].', '
                .' date_depart = \''.$datas['date_depart'].'\', '
                .' date_retour = \''.$datas['date_retour'].'\', '
                .' price = '.$datas['price']
                .' WHERE concert_id = '.$this->securize($datas['covoit_id']);
        }
        return $this->execute($sql);
    }
    
    public function create($items)
    {
        $sql = 'INSERT INTO covoit (concert_id,
                                    user_id,
                                    ville_id,
                                    date_depart,
                                    date_retour,
                                    price) 
                VALUES ('.$items['concert_id'].',
                         '.$this->getContextUser('id').',
                         '.$items['ville_id'].',
                         "'.$items['date_depart'].'",
                         "'.$items['date_retour'].'",
                         '.$items['price'].');';
        return $this->execute($sql);
    }
}
