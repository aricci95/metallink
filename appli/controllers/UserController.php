<?php

require_once ROOT_DIR . '/appli/controllers/SearchController.php';

class UserController extends SearchController
{
    protected $_type = SEARCH_TYPE_USER;

    protected $_searchParams = array(
        'search_login',
        'search_distance',
        'search_gender',
        'search_age',
    );

}
