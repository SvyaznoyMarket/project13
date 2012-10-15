<?php

namespace Logger;

class DefaultLogger implements LoggerInterface {
    const LEVEL_ERROR = 1;
    const LEVEL_WARN = 2;
    const LEVEL_INFO = 3;
    const LEVEL_DEBUG = 4;

    /** @var \Logger\Appender\AppenderInterface */
    protected $appender;
    protected $name;
    protected $level = 4;

    protected $messages = array();

    public function __construct($appender, $name, $level) {
        $this->appender = $appender;
        $this->name = $name;
        $this->level = $level;
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

        $this->messages[] = array(
            'time'    => date('M d H:i:s'),
            'name'    => $this->name,
            'level'   => $level,
            'message' => is_array($message) ? json_encode($message) : (string)$message,
        );
    }

    public function dump() {
        if ((bool)$this->messages) {
            $this->appender->dump($this->messages);
        }
    }
}
