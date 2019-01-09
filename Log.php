<?php
namespace ank;

class Log
{
    private static $_instance = null;
    private function __construct()
    {

    }
    public static function getInstance()
    {
        if (self::$_instance == null) {
            $_instance = new self;
        }
        return $_instance;
    }
    public static function write($str, $type = 'log')
    {
        if (!App::config('log.start')) {
            return;
        }
        $log_level = App::config('log.level');
        if ($log_level && !in_array($type, $log_level)) {
            return;
        }
        $log_path = App::config('log.path');
        if (!$log_path) {
            $log_path = DATA_PATH . '/logs';
        }
        $cli = '';
        if (IS_CLI) {
            $cli = 'cli_';
        }
        //这个地方因为路由模块还没有初始化完成可能会进入死循环
        if (Route::hasInstance()) {
            $file_path = $log_path . '/' . Route::getInstance()->getModule();
        } else {
            $file_path = $log_path . '/unknow';
        }
        if (!file_exists($file_path)) {
            mkdir($file_path, 0777, true);
        }
        $file_path .= '/' . date('y-m-d') . '_' . $cli . $type . '.log';
        if (!is_string($str)) {
            //为啦方便查看,中文不转义
            $str = json_encode($str, JSON_UNESCAPED_UNICODE);
        }
        $time   = date('Y-m-d H:i:s : ');
        $logstr = "{$time}{$str}\r\n";
        file_put_contents($file_path, $logstr, FILE_APPEND);
    }
}
