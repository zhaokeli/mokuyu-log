<?php
declare (strict_types = 1);

namespace mokuyu;

use Psr\Log\LoggerInterface;

class Log implements LoggerInterface
{
    const ALERT = 'alert';

    const CRITICAL = 'critical';

    const DEBUG = 'debug';

    const EMERGENCY = 'emergency';

    const ERROR = 'error';

    const INFO = 'info';

    const NOTICE = 'notice';

    const WARNING = 'warning';

    /**
     * 日志头,每次写文件时会写到日志内容前面
     * @var null
     */
    protected $header = null;

    /**
     * ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
     * 如果设置的话，只记录这些级别的日志
     * @var array
     */
    protected $level = [];

    protected $log = [];

    /**
     * 日志保存路径
     * @var string
     */
    protected $logPath = __dir__ . '/logs/{Y}{m}/{d}/{type}';

    /**
     * 是否记录日志
     * @var bool
     */
    protected $start = true;

    public function __construct($config)
    {
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }

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
     * 清空日志
     * @authname [权限名字]     0
     * @DateTime 2019-10-28
     * @Author   mokuyu
     * @param string|null $type
     * @return void [type]
     */
    public function clear(string $type = null): void
    {
        if ($type === null) {
            $this->log = [];
        }
        elseif (isset($this->log[$type])) {
            $this->log[$type] = [];
        }
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
     * 日志头信息,每次写日志操作会追加在前面
     * @param string|null $str
     * @return null |null
     */
    public function header(string $str = null)
    {
        if ($str === null) {
            return $this->header;
        }
        else {
            $this->header = PHP_EOL . $str;
        }

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
     * 设置当前记录的日志等级
     * @param array|null $value
     * @return array
     */
    public function level(array $value = null)
    {
        if ($value === null) {
            return $this->level;
        }
        else {
            $this->level = $value;
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
     * 最终保存到硬盘上
     * @authname [权限名字]     0
     * @DateTime 2019-10-28
     * @Author   mokuyu
     * @return void [type]
     */
    public function save(): void
    {
        if (!$this->start) {
            return;
        }
        $logstr      = '';
        $pathReplace = [
            '{y}' => date('y'),
            '{Y}' => date('Y'),
            '{m}' => date('m'),
            '{d}' => date('d'),

        ];
        $isSingle    = strpos($this->logPath, '{type}') === false ? false : true;
        foreach ($this->log as $type => $value) {
            if ($this->level && !in_array($type, $this->level)) {
                continue;
            }
            foreach ($value as $k => $v) {
                if (!is_string($v)) {
                    $v = json_encode($v, JSON_UNESCAPED_UNICODE);
                }
                $logstr .= PHP_EOL . '[' . $type . '] ' . PHP_EOL . $v;
            }
            if ($isSingle) {
                $pathReplace['{type}'] = $type;
                //替换成真实路径
                $this->toFile($logstr, $pathReplace);
                $logstr = '';
            }

            //多线程
            // $fp = fopen($logPath, 'a');
            // if (flock($fp, LOCK_EX)) {
            //     fwrite($fp, $logstr);
            //     flock($fp, LOCK_UN);
            // }
            // fclose($fp);
        }
        if (!$isSingle) {
            $this->toFile($logstr, $pathReplace);
        }
        $this->log = [];

    }

    /**
     * 是否开启日志记录
     * @param bool $value
     */
    public function start(bool $value = true): void
    {
        $this->start = $value;
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
     * 写入指定类型日志
     * @param        $message
     * @param string $type
     * @param array  $context
     */
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
        //命令行模式下，实时保存，防止内存溢出
        PHP_SAPI === 'cli' && $this->save();
    }

    /**
     * 把日志写入文件
     * @param string $content
     * @param array  $pathReplace
     */
    protected function toFile(string $content, array $pathReplace): void
    {
        $header = '';
        if ($this->header === null) {
            $header = PHP_EOL . str_repeat('-', 30) . PHP_EOL . '[' . date('Y-m-d H:i:s') . ']';
        }
        else {
            $header = $this->header;
        }
        $logPath = strtr($this->logPath, $pathReplace) . '.log';
        $dirPath = dirname($logPath);
        if (!is_dir($dirPath)) {
            @mkdir($dirPath, 0777, true);
            @chmod($dirPath, 0777);
        }
        file_put_contents($logPath, $header . $content, FILE_APPEND);
    }
}
