<?

class EngineObject
{

    public $log;

    public function __construct()
    {
        $this->log = new Log();
    }

    public function getContextUser($attribute = null)
    {
        if (!empty($attribute) && array_key_exists('user_'.$attribute, $_SESSION) && !empty($_SESSION['user_'.$attribute])) {
            return $_SESSION['user_'.$attribute];
        } elseif ($attribute == 'role_id' && !empty($_SESSION['role_id'])) {
            return $_SESSION['role_id'];
        } else {
            if (!empty($_SESSION['user_id'])) {
                return array('id' => $_SESSION['user_id'],
                            'login' => $_SESSION['user_login'],
                            'last_connexion' => $_SESSION['user_last_connexion'],
                            'role_id' => $_SESSION['role_id'],
                            'photo_url' => empty($_SESSION['user_photo_url']) ? 'unknowUser.jpg' : $_SESSION['user_photo_url'],
                            'age' => $_SESSION['age'],
                            'gender' => $_SESSION['user_gender'],
                            'city'   => $_SESSION['user_city'],
                            'zipcode'   => $_SESSION['user_zipcode']);
            }
        }
        return null;
    }

    public function getLinkStatus($userId)
    {
        return (!empty($_SESSION['links'][$userId])) ? $_SESSION['links'][$userId] : LINK_STATUS_NONE;
    }

    public function debug($var)
    {
        echo '<div class="debug">';
            echo nl2br($var);
        echo '</div>';
    }
}
