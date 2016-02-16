<?php

class Link extends AppModel
{

    public function linkTo($destinataire)
    {
        $links = $this->context->get('links');

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
                'context_user_id' => $this->context->get('user_id'),
                'user_id' => $destinataire['user_id'],
                'link_status_sent' => LINK_STATUS_SENT,
            );

            $this->execute($sql, $params);

            $links[$this->insertId()] = LINK_STATUS_SENT;
        } catch (Exception $e) {
            if ($this->unlink($destinataire['user_id'])) {
                $sql  = "INSERT INTO link (expediteur_id, destinataire_id, status, modification_date) VALUES ('".$this->context->get('user_id')."', '".$this->securize($destinataire['user_id'])."', ".LINK_STATUS_SENT.", NOW());";

                if ($this->execute($sql)) {
                    $links[$this->insertId()] = LINK_STATUS_SENT;
                } else {
                    throw $e;
                    return false;
                }
            }
        }

        if (!empty($destinataire['user_mail'])) {
            $message = 'Vous avez reçu une nouvelle demande de la part de '. $this->context->get('user_login') .' !';
            Mailer::send($destinataire['user_mail'], 'Nouvelle demande sur MetalLink !', $message);
        }

        $this->context->set('links', $links);

        return true;
    }

    public function block($userId)
    {
        $sql  = "INSERT INTO link (expediteur_id, destinataire_id, status, modification_date) VALUES ('".$this->context->get('user_id')."', '".$this->securize($userId)."', ".LINK_STATUS_BLACKLIST.", NOW());";

        if ($this->execute($sql)) {
            $links = $this->context->get('links');

            $links[$userId] = LINK_STATUS_BLACKLIST;

            $this->context->set('links', $links);

            return true;
        } else {
            return false;
        }
    }

    public function unlink($destinataireId)
    {
        $sql = "DELETE FROM link
                WHERE (
                    expediteur_id = :context_user_id
                    AND destinataire_id = :destinataire_id
                ) OR (
                    destinataire_id = :context_user_id
                    AND expediteur_id = :destinataire_id
                );";

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue(':context_user_id', $this->context->get('user_id'), PDO::PARAM_INT);
        $stmt->bindValue(':destinataire_id', $destinataireId, PDO::PARAM_INT);

        Db::executeStmt($stmt);

        $links = $this->context->get('links');

        unset($links[$destinataireId]);

        $this->context->set('links', $links);

        return true;
    }

    public function isLinked($destinataireId)
    {
        $contextId = $this->context->get('user_id');

        if ($destinataireId == 1 || $contextId == 1) {
            return true;
        }

        return (Link::getStatus($destinataireId) == LINK_STATUS_ACCEPTED);
    }

    public static function getStatus($userId)
    {
        $links = $this->context->get('links');

        return !empty($links[$userId]) ? $links[$userId] : LINK_STATUS_NONE;
    }

    public function getLink($userId2)
    {
        $sql = "SELECT
                    expediteur_id,
                    destinataire_id,
                    status,
                    modification_date
                FROM link
                WHERE destinataire_id = :context_user_id
                AND expediteur_id = :user_id
                OR expediteur_id = :context_user_id
                AND destinataire_id = :user_id;";

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue(':context_user_id', $this->context->get('user_id'), PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId2, PDO::PARAM_INT);

        return Db::executeStmt($stmt)->fetch();
    }

    public function updateLink($destinataireId, $status)
    {
        $sql = "UPDATE link SET status = $status,
                    destinataire_id = :destinataire_id,
                    expediteur_id = :context_user_id
                WHERE (
                    destinataire_id = :context_user_id
                    OR destinataire_id = :destinataire_id
                )
                AND (
                    expediteur_id = :destinataire_id
                    OR expediteur_id = :context_user_id
            );";

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue(':context_user_id', $this->context->get('user_id'), PDO::PARAM_INT);
        $stmt->bindValue(':destinataire_id', $destinataireId, PDO::PARAM_INT);

        if (Db::executeStmt($stmt)) {
            $links = $this->context->get('links');

            $links[$destinataireId] = $status;

            $this->context->set('links', $links);

            return true;
        } else {
            return false;
        }
    }

    public function getLinksByUser($status = null)
    {
        $userId = $this->context->get('user_id');

        $sql = "SELECT * FROM link
                WHERE (destinataire_id = '".$this->securize($userId)."' OR link.expediteur_id = '".$this->securize($userId)."') ";
        if ($status > 0) {
            $sql .= " AND status = ".$status." ";
        }
         $sql .= " ORDER BY status DESC;";
        return $this->fetch($sql);
    }

    public function setContextUserLinks()
    {
        $userId = !empty($userId) ? $userId : $this->context->get('user_id');

        $sql = 'SELECT * FROM link
                WHERE (destinataire_id = :user_id OR link.expediteur_id = :user_id)
                ORDER BY status;';

        $stmt = Db::getInstance()->prepare($sql);

        $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);

        $links = Db::executeStmt($stmt)->fetchAll();

        // initialisation des valeurs
        $return = array('count' => array(LINK_STATUS_NONE => 0,
                                         LINK_STATUS_SENT => 0,
                                         LINK_STATUS_RECEIVED => 0,
                                         LINK_STATUS_ACCEPTED => 0,
                                         LINK_STATUS_BLACKLIST => 0,
                                         LINK_STATUS_BLACKLISTED => 0));

        foreach ($links as $key => $link) {
            $newKey = ($link['expediteur_id'] != $userId) ? $link['expediteur_id'] : $link['destinataire_id'];

            if ($link['status'] == LINK_STATUS_SENT) {
                $status = ($link['expediteur_id'] != $userId) ? LINK_STATUS_RECEIVED : LINK_STATUS_SENT;
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

        $this->context->set('links', $return);

        $this->context->set('links_count_received', $return['count'][LINK_STATUS_RECEIVED]);
        $this->context->set('links_count_accepted', $return['count'][LINK_STATUS_ACCEPTED]);
        $this->context->set('links_count_blacklist', $return['count'][LINK_STATUS_BLACKLIST]);

        return $return;
    }

    public function getLinksUserByStatus($status, $offset = 0)
    {
        switch ($status) {
            case LINK_STATUS_RECEIVED:
                $param = " FROM link JOIN user ON (link.expediteur_id = user.user_id)
                           WHERE link.destinataire_id = '".$this->securize($this->context->get('user_id'))."'
                           AND status = ".LINK_STATUS_SENT;
                break;
            case LINK_STATUS_SENT:
                $param = " FROM link JOIN user ON (link.destinataire_id = user.user_id )
                           WHERE link.expediteur_id = '".$this->securize($this->context->get('user_id'))."'
                           AND status = ".LINK_STATUS_SENT;
                break;
            case LINK_STATUS_ACCEPTED:
                $param = " FROM link, user
                           WHERE (link.destinataire_id = '".$this->context->get('user_id')."' OR link.expediteur_id = '".$this->context->get('user_id')."')
                           AND (link.destinataire_id = user.user_id OR link.expediteur_id = user.user_id)
                           AND status = ".LINK_STATUS_ACCEPTED;
                break;
            case LINK_STATUS_BLACKLIST:
                $param = " FROM link JOIN user ON (link.destinataire_id = user.user_id )
                           WHERE link.expediteur_id = '".$this->context->get('user_id')."'
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
                .' AND user.user_id != '.$this->context->get('user_id')
                .' ORDER BY modification_date DESC
                LIMIT '.($offset * NB_SEARCH_RESULTS).', '.NB_SEARCH_RESULTS.';';
        return $this->fetch($sql);
    }
}
