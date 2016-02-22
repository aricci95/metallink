<?php
class Log
{
    const LOG_FILE_SIZE_LIMIT = 1000000;

    private $_path;

    private static $_instance = null;

    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new Log();
        }

        return self::$_instance;
    }

    public function __construct()
    {
        $this->_path = ROOT_DIR . '/logs/' . LOG_FILE;

        // Flush log file if needed
        if (!file_exists($this->_path) || filesize($this->_path) > self::LOG_FILE_SIZE_LIMIT) {
            $file = fopen($this->_path, "w");
            fwrite($file, "[" . date('d/m H:i') ."] : File flushed \n");
            fclose($file);
        }
    }

    public static function debug($data)
    {
        self::getInstance()->write('debug', $data);
    }

    public static function info($data)
    {
        self::getInstance()->write('info', $data);
    }

    public static function err($data)
    {
        self::getInstance()->write('err', $data);
    }

    public static function hack($data)
    {
        self::getInstance()->write('hack', $data);
    }

    public static function php($data)
    {
        self::getInstance()->write('php', $data);
    }

    public static function warn($data)
    {
        self::getInstance()->write('warn', $data);
    }


    public function write($logName = 'info', $data)
    {
        if (!LOG) {
            return null;
        }

        $logLevel = constant('LOG_LEVEL_' . strtoupper($logName));
        $date     = date('d/m H:i');

        $backtrace = debug_backtrace();
        $params    = '';
        $from      = '['.$date.']['. strtoupper($logName) .'] : ';

        if (!empty($backtrace[2]['args'])) {
            foreach ($backtrace[2]['args'] as $key => $value) {
                if (is_array($value)) {
                    $backtrace[2]['args'][$key] = 'Array';
                } elseif (is_object($value)) {
                    $backtrace[2]['args'][$key] = get_class($value);
                }
            }
            $params = implode(',', $backtrace[2]['args']);
        }

        if (!empty($backtrace[2]['class'])) {
            $from .= $backtrace[2]['class'].'->';
        }

        if (!empty($backtrace[2]['function'])) {
            $from .= $backtrace[2]['function'].'('.$params.') ';
        }

        if (!empty($backtrace[2]['line'])) {
            $from .= $backtrace[2]['line'];
        }

        $from .= ' : ';

        if (is_array($data)) {
            $values = str_replace(':', ' => ', json_encode($data));
            error_log($from . 'array '. $values ."\n", 3, $this->_path);
        } elseif (is_object($data)) {
            $values = str_replace(':', ' => ', json_encode($data));
            error_log($from . get_class($data).' '. $values ."\n", 3, $this->_path);
        } else {
            error_log($from . $data ."\n", 3, $this->_path);
        }
    }
}
