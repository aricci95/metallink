<?php

class AuthService extends Service
{

    public function login($login, $pwd)
    {
        $login = trim($this->context->params['user_login']);

        $logResult = $this->checkLogin($login, md5($this->context->params['user_pwd']));

        if ($logResult) {
            if ($this->context->getParam('savepwd') == 'on') {
                setcookie('MlinkLogin', $this->context->getParam('user_login'), time() + 365*24*3600, '/', null, false, true);
                setcookie('MlinkPwd', md5($this->context->getParam('user_pwd')), time() + 365*24*3600, '/', null, false, true);
            } else {
                setcookie('MlinkLogin', 0, time(), '/', false, true);
                setcookie('MlinkPwd', 0, time(), '/', false, true);
            }

            return true;
        }

        return false;
    }

    public function checkLogin($login, $pwd)
    {
        $user = $this->model->user->findByLoginPwd($login, $pwd);

        if (!empty($user['user_login']) && !empty($user['user_id']) && strtolower($user['user_login']) == strtolower($login) && $login != '') {
            if ($user['user_valid'] != 1) {
                throw new Exception("Email non validé", ERR_MAIL_NOT_VALIDATED);
            } elseif ($user['role_id'] > 0) {
                $localization = $this->get('geoloc')->localize();

                if (!empty($localization) && $localization->postal_code !== $user['ville_code_postal']) {
                    $ville = $this->model->city->findOne(array('ville_longitude_deg', 'ville_latitude_deg', 'ville_id'), array('%ville_code_postal' => $localization->postal_code));

                    $this->model->user->updateUserLocalization($user);
                } else {
                    $ville = $this->model->city->findOne(array('ville_longitude_deg', 'ville_latitude_deg'), array('ville_id' => $user['ville_id']));

                    $this->model->user->updateLastConnexion();
                }

                $user['ville_longitude_deg'] = $ville['ville_longitude_deg'];
                $user['ville_latitude_deg'] = $ville['ville_latitude_deg'];

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
                      ->set('ville_id', (int) $user['ville_id'])
                      ->set('ville_longitude_deg', $user['ville_longitude_deg'])
                      ->set('ville_latitude_deg', $user['ville_latitude_deg'])
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
        setcookie('MlinkLogin', 0, time(), '/', false, true);
        setcookie('MlinkPwd', 0, time(), '/', false, true);

        $this->context->destroy();

        return true;
    }
}
