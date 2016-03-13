<?php

require_once ROOT_DIR . '/appli/controllers/SearchController.php';

class ConcertController extends SearchController
{
    protected $_type = SEARCH_TYPE_CONCERT;

    protected $_searchParams = array(
        'search_distance',
        'search_keyword',
        'search_style',
    );
}
