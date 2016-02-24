<?php

class AuthService extends Service
{

    public function checkLogin($login, $pwd)
    {
        if (!empty($login) && !empty($pwd)) {
            $user = $this->model->user->findByLoginPwd($login, $pwd);

            if (!empty($user['user_login']) && !empty($user['user_id']) && strtolower($user['user_login']) == strtolower($login) && $login != '') {
                $this->model->user->updateLastConnexion();

                if ($user['user_valid'] != 1) {
                    throw new Exception("Email non validé", ERR_MAIL_NOT_VALIDATED);
                } elseif ($user['role_id'] > 0) {
                    $this->context->set('user_id', (int) $user['user_id'])
                                  ->set('user_login', $user['user_login'])
                                  ->set('user_pwd', $pwd)
                                  ->set('user_last_connexion', time())
                                  ->set('role_id', (int) $user['role_id'])
                                  ->set('user_photo_url', empty($user['user_photo_url']) ? 'unknowUser.jpg' : $user['user_photo_url'])
                                  ->set('age', (int) $user['age'])
                                  ->set('user_valid', (int) $user['user_valid'])
                                  ->set('user_mail', $user['user_mail'])
                                  ->set('user_gender', (int) $user['user_gender'])
                                  ->set('user_city', $user['user_city'])
                                  ->set('user_zipcode', (int) $user['user_zipcode'])
                                  ->set('user_longitude', $user['longitude'])
                                  ->set('user_lattitude', $user['lattitude'])
                                  ->set('forum_notification', $user['forum_notification']);
                    return true;
                }
            } else {
                throw new Exception("Mauvais login / mot de passe", ERR_LOGIN);
            }
        } else {
            throw new Exception("Mauvais login / mot de passe", ERR_LOGIN);
        }

        return false;
    }

    // Renvoi le mdp
    public function sendPwd($login = null, $message = null)
    {
        if (empty($login) && empty($message)) {
            return false;
        }

        $param = (empty($login)) ? 'user_mail' : 'user_login';
        $value = (empty($login)) ? $message : $login;

        $result = $this->model->user->find(array(
            'user_id',
            'user_login',
            'user_mail'
            ),
            array($param => $value)
        );

        if (!empty($result['user_login'])) {
            $pwd_valid = $this->model->auth->resetPwd($result['user_id']);

            $message = 'Pour modifier ton mot de passe clique sur le lien suivant : <a href="http://www.metallink.fr/lostpwd/new/' . $pwd_valid . '">modifier mon mot de passe</a>';

            return $this->get('mailer')->send($result['user_mail'], 'Modifcation du mot de passe MetalLink', $message);
        } else {
            return false;
        }
    }
}
