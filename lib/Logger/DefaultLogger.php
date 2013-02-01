<?php

namespace Logger;

class DefaultLogger implements LoggerInterface {
    const LEVEL_ERROR = 1;
    const LEVEL_WARN = 2;
    const LEVEL_INFO = 3;
    const LEVEL_DEBUG = 4;

    /** @var string */
    protected $id;
    /** @var \Logger\Appender\AppenderInterface */
    protected $appender;
    /** @var string */
    protected $name;
    /** @var int */
    protected $level = 4;
    /** @var array */
    protected $levelNames = [
        0 => '',
        1 => 'error',
        2 => 'warn',
        3 => 'info',
        4 => 'debug',
    ];
    /** @var bool */
    protected $immediatelyDump;

    protected $messages = [];

    public function __construct(\Logger\Appender\AppenderInterface $appender, $name, $level, $immediatelyDump = false) {
        $this->id = \App::$id;
        $this->appender = $appender;
        $this->name = (string)$name;
        $this->level = (int)$level;
        if ($this->level < 0 || $this->level > 4) $this->level = 0;

        $this->immediatelyDump = $immediatelyDump;
    }

    public function debug($message) {
        $this->log($message, self::LEVEL_DEBUG);
    }

    public function info($message) {
        $this->log($message, self::LEVEL_INFO);
    }

    public function warn($message) {
        $this->log($message, self::LEVEL_WARN);
    }

    public function error($message) {
        $this->log($message, self::LEVEL_ERROR);
    }

    protected function log($message, $level) {
        if ($level > $this->level) return;

        $logData = [
            'time'    => date('M d H:i:s'),
            //'name'    => $this->name,
            'name'    => $this->id,
            'level'   => $this->levelNames[$level],
            'message' => is_array($message) ? json_encode($message, JSON_UNESCAPED_UNICODE) : (string)$message,
        ];
        if ($this->immediatelyDump) {
            $this->appender->dump([$logData]);
        } else {
            $this->messages[] = $logData;
        }
    }

    public function dump() {
        if ((bool)$this->messages) {
            $this->appender->dump($this->messages);
        }
    }

    public function getMessages() {
        return $this->messages;
    }
}
