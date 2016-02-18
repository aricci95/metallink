<?php

Class UserService extends Service
{

    public function delete($id)
    {
        if (!$this->model->user->deleteById($id)) {
            return false;
        }

        return $this->get('photo')->deleteFromUser($id);
    }
}
