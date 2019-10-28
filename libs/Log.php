<?php
declare (strict_types = 1);
namespace ank;

use Psr\Log\LoggerInterface;

class Log implements LoggerInterface
{
    /**
     * 日志保存路径
     * @var string
     */
    private $logPath = __dir__ . '/logs/{Y}{m}/{d}/{type}';
    private $log     = [];
    public function __construct($config)
    {
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }

    }
    public function write($message, $type = 'info', array $context = []): void
    {
        // 构建一个花括号包含的键名的替换数组
        $replace = [];
        foreach ($context as $key => $val) {
            // 检查该值是否可以转换为字符串
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        // 替换记录信息中的占位符，最后返回修改后的记录信息。
        $message            = strtr($message, $replace);
        $this->log[$type][] = $message;
    }
    /**
     * 最终保存到硬盘上
     * @authname [权限名字]     0
     * @Author   mokuyu
     * @DateTime 2019-10-28
     * @return   [type]
     */
    public function save()
    {
        $logstr = '';
        foreach ($this->log as $type => $value) {
            foreach ($value as $k => $v) {
                if (!is_string($v)) {
                    $v = json_encode($vv, JSON_UNESCAPED_UNICODE);
                }
                $logstr .= PHP_EOL . $v;
            }
            //替换成真实路径
            $logPath = strtr($this->logPath, [
                '{y}'    => date('y'),
                '{Y}'    => date('Y'),
                '{m}'    => date('m'),
                '{d}'    => date('d'),
                '{type}' => $type,
            ]) . '.log';
            file_exists($logPath) || @mkdir(dirname($logPath), 0777, true);
            file_put_contents($logPath, $logstr, FILE_APPEND);
            //多线程
            // $fp = fopen($logPath, 'a');
            // if (flock($fp, LOCK_EX)) {
            //     fwrite($fp, $logstr);
            //     flock($fp, LOCK_UN);
            // }
            // fclose($fp);
        }

    }
    /**
     * 清空日志
     * @authname [权限名字]     0
     * @Author   mokuyu
     * @DateTime 2019-10-28
     * @param    [type]     $type [description]
     * @return   [type]
     */
    public function clear($type = null)
    {
        if ($type === null) {
            $this->log = [];
        } elseif (isset($this->log[$type])) {
            $this->log[$type] = [];
        }
    }
    /**
     * 记录日志信息
     * @access public
     * @param string $level   日志级别
     * @param mixed  $message 日志信息
     * @param array  $context 替换内容
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        $this->write($message, $level, $context);
    }
    /**
     * 记录emergency信息
     * @access public
     * @param mixed $message 日志信息
     * @param array $context 替换内容
     * @return void
     */
    public function emergency($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    /**
     * 记录警报信息
     * @access public
     * @param mixed $message 日志信息
     * @param array $context 替换内容
     * @return void
     */
    public function alert($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    /**
     * 记录紧急情况
     * @access public
     * @param mixed $message 日志信息
     * @param array $context 替换内容
     * @return void
     */
    public function critical($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    /**
     * 记录错误信息
     * @access public
     * @param mixed $message 日志信息
     * @param array $context 替换内容
     * @return void
     */
    public function error($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    /**
     * 记录warning信息
     * @access public
     * @param mixed $message 日志信息
     * @param array $context 替换内容
     * @return void
     */
    public function warning($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    /**
     * 记录notice信息
     * @access public
     * @param mixed $message 日志信息
     * @param array $context 替换内容
     * @return void
     */
    public function notice($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    /**
     * 记录一般信息
     * @access public
     * @param mixed $message 日志信息
     * @param array $context 替换内容
     * @return void
     */
    public function info($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
    /**
     * 记录调试信息
     * @access public
     * @param mixed $message 日志信息
     * @param array $context 替换内容
     * @return void
     */
    public function debug($message, array $context = []): void
    {
        $this->log(__FUNCTION__, $message, $context);
    }
}
