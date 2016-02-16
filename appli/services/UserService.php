<?php

Class UserService extends Service
{

    public function delete($id)
    {
        if (!User::deleteById($id)) {
            return false;
        }

        return $this->get('photo')->deleteFromUser($id);
    }
}
