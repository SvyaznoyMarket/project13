<?php

namespace Logger;

class TimerLogger extends DefaultLogger {
    private $start;
    private $time;

    public function __construct($appender, $name, $level) {
        parent::__construct($appender, $name, $level);

        $this->start = microtime(true);
        $this->time = $this->start;
    }

    protected function log($message, $level) {
        if ($level > $this->level) return;

        $this->time = microtime(true);

        $this->messages[] = array(
            'time'    => date('M d H:i:s').' '.round($this->time - $this->start, 4),
            'name'    => $this->name,
            'level'   => $level,
            'message' => is_array($message) ? json_encode($message, JSON_UNESCAPED_UNICODE) : (string)$message,
        );
    }

}
