<?php

class Context
{

    private static $_instance = null;

    private $_data;

    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct()
    {
        $init_vars = array(
            'user_id' => null,
            'user_valid' => false,
            'user_login' => null,
            'user_last_connexion' => null,
            'role_id' => 0,
            'user_photo_url' => 'unknowUser.jpg',
            'user_age' => null,
            'user_gender' => null,
            'user_city'   => null,
            'user_zipcode'   => null,
            'links' => array(),
            'links_count_received' => 0,
            'links_count_accepted' => 0,
            'links_count_blacklist' => 0,
            'last_forum_message_id' => null,
            'last_forum_message_date' => null,
            'new_messages' => 0,
            'forum_notification' => true,
            'views' => 0,
            'search_login' => null,
            'search_distance' => null,
            'search_gender' => null,
            'search_age' => null,
        );

        $this->_data = array_merge($init_vars, $_SESSION);
    }

    public function set($key, $value)
    {
        $this->_data[$key] = $_SESSION[$key] = $value;

        return $this;
    }

    public function get($key)
    {
        return $this->_data[$key];
    }
}
