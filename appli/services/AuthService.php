<?php

class AuthService extends Service
{

    public function checkLogin($login, $pwd)
    {
        $user = $this->model->user->findByLoginPwd($login, $pwd);

        if (!empty($user['user_login']) && !empty($user['user_id']) && strtolower($user['user_login']) == strtolower($login) && $login != '') {
            $this->model->user->updateLastConnexion();

            if ($user['user_valid'] != 1) {
                throw new Exception("Email non validé", ERR_MAIL_NOT_VALIDATED);
            } elseif ($user['role_id'] > 0) {
                return $this->authenticateUser($user);
            }
        } else {
            throw new Exception("Mauvais login / mot de passe", ERR_LOGIN);
        }

        return false;
    }

    public function authenticateUser(array $user)
    {
        $this->context->set('user_id', (int) $user['user_id'])
                      ->set('user_login', $user['user_login'])
                      ->set('user_pwd', $user['user_pwd'])
                      ->set('user_last_connexion', time())
                      ->set('role_id', (int) $user['role_id'])
                      ->set('user_photo_url', empty($user['user_photo_url']) ? 'unknowUser.jpg' : $user['user_photo_url'])
                      ->set('age', (int) $user['age'])
                      ->set('user_valid', (int) $user['user_valid'])
                      ->set('user_mail', $user['user_mail'])
                      ->set('user_gender', (int) $user['user_gender'])
                      ->set('user_city', $user['user_city'])
                      ->set('ville_id', (int) $user['ville_id'])
                      ->set('user_zipcode', (int) $user['user_zipcode'])
                      ->set('user_longitude', $user['longitude'])
                      ->set('user_lattitude', $user['lattitude'])
                      ->set('forum_notification', $user['forum_notification']);
        return true;
    }

    // Renvoi le mdp
    public function sendPwd($login = null, $email = null)
    {
        if (empty($login) && empty($email)) {
            return false;
        }

        $param = empty($login) ? 'user_mail' : 'user_login';
        $value = empty($login) ? $email : $login;

        $result = $this->model->user->find(array(
            'user_id',
            'user_login',
            'user_mail'
            ),
            array($param => $value)
        );
        
        $user = $result[0];

        if (!empty($user['user_login'])) {
            $pwd_valid = $this->model->auth->resetPwd($user['user_id']);

            $message = 'Pour modifier ton mot de passe clique sur le lien suivant : <a href="http://www.metallink.fr/lostpwd/new/' . $pwd_valid . '">modifier mon mot de passe</a>';

            return $this->get('mailer')->send($user['user_mail'], 'Modifcation du mot de passe MetalLink', $message);
        } else {
            return false;
        }
    }

    public function disconnect()
    {
        //Destruction du Cookie
        setcookie("MlinkPwd", 0);
        setcookie("MlinkLogin", 0);

        $this->context->destroy();

        return true;
    }
}
