<?php

class SocialController extends Controller
{
    private $_app;

    public function renderLogin()
    {
        $this->_app = $this->params['value'];

        $this->get($this->_app)->login();
    }

    public function renderSession()
    {
        echo '<pre>' . print_r($_SESSION["userprofile"], true) . '</pre>';die;
    }
}
