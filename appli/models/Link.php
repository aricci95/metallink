<?php

class Link extends AppModel
{

    public function linkTo($destinataire)
    {
        $sql  = '
            INSERT INTO
                link (
                    expediteur_id,
                    destinataire_id,
                    status,
                    modification_date
                )
            VALUES (
                :context_user_id,
                :user_id,
                :link_status_sent,
                NOW()
            )
        ;';

        try {
            $params = array(
                'context_user_id' => User::getContextUser('id'),
                'user_id' => $destinataire['user_id'],
                'link_status_sent' => LINK_STATUS_SENT,
            );

            $this->execute($sql, $params);

            $_SESSION['links'][$this->insertId()] = LINK_STATUS_SENT;
        } catch (Exception $e) {
            if ($this->unlink($destinataire['user_id'])) {
                $sql  = "INSERT INTO link (expediteur_id, destinataire_id, status, modification_date) VALUES ('".User::getContextUser('id')."', '".$this->securize($destinataire['user_id'])."', ".LINK_STATUS_SENT.", NOW());";
                if ($this->execute($sql)) {
                    $_SESSION['links'][$this->insertId()] = LINK_STATUS_SENT;
                } else {
                    throw $e;
                    return false;
                }
            }
        }
        if (!empty($destinataire['user_mail'])) {
            $message = 'Vous avez reçu une nouvelle demande de la part de '. User::getContextUser('login') .' !';
            Mailer::send($destinataire['user_mail'], 'Nouvelle demande sur MetalLink !', $message);
        }
        return true;
    }

    public function block($userId)
    {
        $sql  = "INSERT INTO link (expediteur_id, destinataire_id, status, modification_date) VALUES ('".User::getContextUser('id')."', '".$this->securize($userId)."', ".LINK_STATUS_BLACKLIST.", NOW());";
        if ($this->execute($sql)) {
            $_SESSION['links'][$userId] = LINK_STATUS_BLACKLIST;
            return true;
        } else {
            return false;
        }
    }

    public function unlink($destinataireId)
    {
        $sql = "DELETE FROM link WHERE (expediteur_id = ".User::getContextUser('id')." AND destinataire_id = '".$destinataireId."')
                                 OR (destinataire_id = ".User::getContextUser('id')." AND expediteur_id = '".$destinataireId."');";
        if ($this->execute($sql)) {
            unset($_SESSION['links'][$destinataireId]);
            return true;
        } else {
            return false;
        }
    }

    public function isLinked($destinataireId)
    {
        $contextId = User::getContextUser('id');

        if ($destinataireId == 1 || $contextId == 1) {
            return true;
        }

        return (Link::getStatus($destinataireId) == LINK_STATUS_ACCEPTED);
    }

    public static function getStatus($userId)
    {
        return (!empty($_SESSION['links'][$userId])) ? $_SESSION['links'][$userId] : LINK_STATUS_NONE;
    }

    public function getLink($userId2)
    {
        $sql = "SELECT
                    expediteur_id,
                    destinataire_id,
                    status,
                    modification_date
                FROM link
                WHERE destinataire_id = '".User::getContextUser('id')."'
                AND expediteur_id = '".$this->securize($userId2)."'
                OR expediteur_id = '".User::getContextUser('id')."'
                AND destinataire_id = '".$this->securize($userId2)."';";
        return $this->fetchOnly($sql);
    }

    public function updateLink($destinataireId, $status)
    {
        $sql = "UPDATE link SET status = $status,
                                destinataire_id = ".$destinataireId.",
                                expediteur_id = ".User::getContextUser('id')."
                WHERE (destinataire_id = '".User::getContextUser('id')."' OR destinataire_id = '".$destinataireId."')
                AND   (expediteur_id = '".$this->securize($destinataireId)."' OR expediteur_id = '".User::getContextUser('id')."');";
        if ($this->execute($sql)) {
            $_SESSION['links'][$destinataireId] = $status;
            return true;
        } else {
            return false;
        }
    }

    public function getLinksByUser($status = null)
    {
        $userId = User::getContextUser('id');
        $sql = "SELECT * FROM link
                WHERE (destinataire_id = '".$this->securize($userId)."' OR link.expediteur_id = '".$this->securize($userId)."') ";
        if ($status > 0) {
            $sql .= " AND status = ".$status." ";
        }
         $sql .= " ORDER BY status DESC;";
        return $this->fetch($sql);
    }

    public function setContextUserLinks($userId = null)
    {
        $userId = (!empty($userId)) ? $userId : User::getContextUser('id');
        $sql = "SELECT * FROM link
                WHERE (destinataire_id = '".$this->securize($userId)."' OR link.expediteur_id = '".$this->securize($userId)."')
                ORDER BY status;";
        $links = $this->fetch($sql);
        // initialisation des valeurs
        $return = array('count' => array(LINK_STATUS_NONE => 0,
                                         LINK_STATUS_SENT => 0,
                                         LINK_STATUS_RECIEVED => 0,
                                         LINK_STATUS_ACCEPTED => 0,
                                         LINK_STATUS_BLACKLIST => 0,
                                         LINK_STATUS_BLACKLISTED => 0));
        foreach ($links as $key => $link) {
            $newKey = ($link['expediteur_id'] != $userId) ? $link['expediteur_id'] : $link['destinataire_id'];
            if ($link['status'] == LINK_STATUS_SENT) {
                $status = ($link['expediteur_id'] != $userId) ? LINK_STATUS_RECIEVED : LINK_STATUS_SENT;
                $return[(int) $newKey] = (int) $status;
                $return['count'][$status]++;
            } elseif ($link['status'] == LINK_STATUS_BLACKLIST) {
                $status = ($link['expediteur_id'] != $userId) ? LINK_STATUS_BLACKLISTED : LINK_STATUS_BLACKLIST;
                $return[(int) $newKey] = (int) $status;
                $return['count'][$status]++;
            } else {
                $return[(int) $newKey] = (int) $link['status'];
                $return['count'][$link['status']]++;
            }
        }
        ksort($return);
        $_SESSION['links'] = $return;
        return $_SESSION['links'];
    }

    public function getLinksUserByStatus($status, $offset = 0)
    {
        switch ($status) {
            case LINK_STATUS_RECIEVED:
                $param = " FROM link JOIN user ON (link.expediteur_id = user.user_id)
                           WHERE link.destinataire_id = '".$this->securize(User::getContextUser('id'))."'
                           AND status = ".LINK_STATUS_SENT;
                break;
            case LINK_STATUS_SENT:
                $param = " FROM link JOIN user ON (link.destinataire_id = user.user_id )
                           WHERE link.expediteur_id = '".$this->securize(User::getContextUser('id'))."'
                           AND status = ".LINK_STATUS_SENT;
                break;
            case LINK_STATUS_ACCEPTED:
                $param = " FROM link, user
                           WHERE (link.destinataire_id = '".User::getContextUser('id')."' OR link.expediteur_id = '".User::getContextUser('id')."')
                           AND (link.destinataire_id = user.user_id OR link.expediteur_id = user.user_id)
                           AND status = ".LINK_STATUS_ACCEPTED;
                break;
            case LINK_STATUS_BLACKLIST:
                $param = " FROM link JOIN user ON (link.destinataire_id = user.user_id )
                           WHERE link.expediteur_id = '".User::getContextUser('id')."'
                           AND status = ".LINK_STATUS_BLACKLIST;
                break;
            default:
                return false;
        }
        $sql = 'SELECT
                    expediteur_id,
                    destinataire_id,
                    modification_date,
                    user_gender,
                    UNIX_TIMESTAMP(user_last_connexion) as user_last_connexion,
                    status,
                    user_id,
                    user_city,
                    user_photo_url,
                    user_login,
                    (YEAR(CURRENT_DATE)-YEAR(user_birth)) - (RIGHT(CURRENT_DATE,5)<RIGHT(user_birth,5)) AS age '.
                $param
                .' AND user.user_id != '.User::getContextUser('id')
                .' ORDER BY modification_date DESC
                LIMIT '.($offset * NB_SEARCH_RESULTS).', '.NB_SEARCH_RESULTS.';';
        return $this->fetch($sql);
    }
}
