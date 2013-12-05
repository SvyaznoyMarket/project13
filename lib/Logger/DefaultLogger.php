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

    public function debug($message, array $tags = []) {
        $this->log($message, self::LEVEL_DEBUG, $tags);
    }

    public function info($message, array $tags = []) {
        $this->log($message, self::LEVEL_INFO, $tags);
    }

    public function warn($message, array $tags = []) {
        $this->log($message, self::LEVEL_WARN, $tags);
    }

    public function error($message, array $tags = []) {
        $this->log($message, self::LEVEL_ERROR, $tags);
    }

    protected function log($message, $level, array $tags = []) {
        if ($level > $this->level) return;

        if ($message instanceof \Exception) {
            $message = [
                'error' => [
                    'code'    => $message->getCode(),
                    'message' => $message->getMessage(),
                    'file'    => $message->getFile() . ' (' . $message->getLine() . ')',
                    'trace'   => $message->getTrace()
                ],
            ];
        }

        $item = [
            '_id'           => $this->id,
            '_time'         => date('M d H:i:s'),
            '_type'         => $this->levelNames[$level],
            '_tag'          => $tags,
            '_timestamp'    => time(),
        ] + (is_array($message) ? $message : ['message' => $message]);

        /*
        $item = [
            'id'      => $this->id,
            'time'    => date('M d H:i:s'),
            'type'    => $this->levelNames[$level],
            'message' => $message,
            'tag'     => (bool)$tags ? ('+' . implode('+', $tags)) : '',
        ];
        */

        if ($this->immediatelyDump) {
            $this->appender->dump([$item]);
        } else {
            $this->messages[] = $item;
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
