<?php

class FacebookService extends Service
{

    public function login()
    {
        $facebookData = $this->context->get('userprofile');

        if (!empty($facebookData['email']) && !empty($facebookData['verified'])) {
            $user = $this->model->user->findByEmail($facebookData['email']);

            if (!empty($user['user_id'])) {
                return $this->get('auth')->authenticateUser($user);
            }
        }
    }

}
