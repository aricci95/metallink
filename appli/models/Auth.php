<?php

/*
 *  Classe d'accès aux données des authentifications
 */

class Auth extends AppModel
{

    // Lance une session pour OVH
    public function startSession()
    {

    }

    public function resetPwd($userId)
    {
        $pwd_valid = uniqid();

        $sql = "
            REPLACE INTO lost_pwd (
                user_id,
                pwd_valid
            ) VALUES (
                :user_id,
                :pwd_valid
            )
        ;";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue('pwd_valid', $pwd_valid, PDO::PARAM_STR);

        if ($this->db->executeStmt($stmt)) {
            return $pwd_valid;
        }
    }

    public function updatePwd($pwd, $pwd_valid)
    {
        if (empty($pwd) || empty($pwd_valid)) {
            return false;
        }

        $query = "
            UPDATE user SET user_pwd = '" . md5($pwd) . "'
            WHERE user_id = (
                SELECT user_id FROM lost_pwd
                WHERE pwd_valid = '" . $pwd_valid . "'
            )
        ;";

        return $this->execute($query);
    }
}
