<?

class EngineObject
{

    public $log;

    public function __construct()
    {
        $this->log = new Log();
    }

    public function debug($var)
    {
        echo '<div class="debug">';
            echo nl2br($var);
        echo '</div>';
    }
}
