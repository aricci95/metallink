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

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('context_user_id', $this->context->get('user_id'), PDO::PARAM_INT);
        $stmt->bindValue('user_id', $destinataire['user_id'], PDO::PARAM_INT);
        $stmt->bindValue('link_status_sent', LINK_STATUS_SENT, PDO::PARAM_INT);

        $this->db->executeStmt($stmt);

        return $this->db->lastInsertId();
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

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':context_user_id', $this->context->get('user_id'), PDO::PARAM_INT);
        $stmt->bindValue(':destinataire_id', $destinataireId, PDO::PARAM_INT);

        $this->db->executeStmt($stmt);

        $links = $this->context->get('links');

        unset($links[$destinataireId]);

        $this->context->set('links', $links);

        return true;
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

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':context_user_id', $this->context->get('user_id'), PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId2, PDO::PARAM_INT);

        return $this->db->executeStmt($stmt)->fetch();
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

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':context_user_id', $this->context->get('user_id'), PDO::PARAM_INT);
        $stmt->bindValue(':destinataire_id', $destinataireId, PDO::PARAM_INT);

        if ($this->db->executeStmt($stmt)) {
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

    public function getUserLinks($userId)
    {
        $sql = 'SELECT * FROM link
                WHERE (destinataire_id = :user_id OR link.expediteur_id = :user_id)
                ORDER BY status;
            ';

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);

        return $this->db->executeStmt($stmt)->fetchAll();
    }

    public function getLinksUserByStatus($status, $offset = 0)
    {
        switch ($status) {
            case LINK_STATUS_RECEIVED:
                $param = " FROM link JOIN user ON (link.expediteur_id = user.user_id)
                           WHERE link.destinataire_id = :context_user_id ";
                $processed_status = LINK_STATUS_SENT;
                break;
            case LINK_STATUS_SENT:
                $param = " FROM link JOIN user ON (link.destinataire_id = user.user_id )
                           WHERE link.expediteur_id = :context_user_id ";
                $processed_status = LINK_STATUS_SENT;
                break;
            case LINK_STATUS_ACCEPTED:
                $param = " FROM link, user
                           WHERE (link.destinataire_id = :context_user_id OR link.expediteur_id = :context_user_id)
                           AND (link.destinataire_id = user.user_id OR link.expediteur_id = user.user_id) ";
                $processed_status = LINK_STATUS_ACCEPTED;
                break;
            case LINK_STATUS_BLACKLIST:
                $param = " FROM link JOIN user ON (link.destinataire_id = user.user_id )
                           WHERE link.expediteur_id = :context_user_id ";
                $processed_status = LINK_STATUS_BLACKLIST;
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
                .'
                AND user.user_id != :context_user_id
                AND status = :processed_status
                AND user_valid = 1
                ORDER BY modification_date DESC
                LIMIT :limit_begin, :limit_end;'
            ;

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('context_user_id', $this->context->get('user_id'), PDO::PARAM_INT);
        $stmt->bindValue('processed_status', $processed_status, PDO::PARAM_INT);
        $stmt->bindValue('limit_begin', $offset * NB_SEARCH_RESULTS, PDO::PARAM_INT);
        $stmt->bindValue('limit_end', NB_SEARCH_RESULTS, PDO::PARAM_INT);

        return $this->db->executeStmt($stmt)->fetchAll();
    }
}
