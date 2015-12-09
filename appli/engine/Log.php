<?php
class Log
{

    public function __call($logName = 'info', $data)
    {
        $logsPath = ROOT_DIR.'/logs/'.LOG_FILE;
        $logLevel = 'LOG_LEVEL_'.strtoupper($logName);
        $date     = date('d/m H:i');

        $backtrace = debug_backtrace();
        $params    = '';
        $from      = '['.$date.']['.strtoupper($logName.'] : ');
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
        if ($logLevel != LOG_LEVEL_PHP) {
            if (count($data) == 1) {
                error_log($from.$data[0]."\n", 3, $logsPath);
            } else {
                $values = str_replace(':', ' => ', json_encode($data));
                error_log($from.'array '.$values."\n", 3, $logsPath);
            }
        } elseif (LOG) {
            if (is_array($data)) {
                $values = str_replace(':', ' => ', json_encode($data));
                error_log($from.'array '.$values."\n", 3, $logsPath);
            } elseif (is_object($data)) {
                $values = str_replace(':', ' => ', json_encode($data));
                error_log($from.get_class($data).' '.$values."\n", 3, $logsPath);
            } else {
                error_log($from.$data."\n", 3, $logsPath);
            }
        }
    }
}
