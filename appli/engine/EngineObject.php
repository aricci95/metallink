<?

class EngineObject
{

    public $log;

    public function __construct()
    {
        $this->log = new Log();
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
