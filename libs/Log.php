<?php
namespace ank;

class Log
{
    private $logPath = '';
    public function __construct($logPath = '')
    {
        $logPath = $logPath;

    }
    public function write($str, $type = 'log')
    {

        $cli = '';
        if (IS_CLI) {
            $cli = 'cli_';
        }
        if (!file_exists($this->logPath)) {
            mkdir($this->logPath, 0777, true);
        }
        $filePath = $this->logPath . '/' . date('y-m-d') . '_' . $cli . $type . '.log';
        if (!is_string($str)) {
            //为啦方便查看,中文不转义
            $str = json_encode($str, JSON_UNESCAPED_UNICODE);
        }
        $time   = date('Y-m-d H:i:s : ');
        $logstr = "{$time}{$str}\r\n";
        file_put_contents($filePath, $logstr, FILE_APPEND);
    }
}
