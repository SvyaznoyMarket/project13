<?php

namespace Logger;

class DefaultLogger implements LoggerInterface {
    const LEVEL_ERROR = 1;
    const LEVEL_WARN = 2;
    const LEVEL_INFO = 3;
    const LEVEL_DEBUG = 4;

    /** @var string */
    protected $id;
    /** @var string */
    protected $parentId;
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
        $this->parentId = \App::request() ? \App::request()->query->get('parent_ri') : null;
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

        // exception check
        if ($message instanceof \Exception) {
            $message = [
                'error' => $this->dumpException($message),
            ];
        } else if (isset($message['error']) && ($message['error'] instanceof \Exception)) {
            $message['error'] = $this->dumpException($message['error']);
        }

        $item = [
            '_id'           => $this->id,
            '_parent'       => $this->parentId,
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

    private function dumpException(\Exception $exception) {
        if ($exception instanceof \EnterQuery\Exception) {
            $message['detail'] = $exception->getDetail();
            $message['curl'] = $exception->getQuery();
        } else if ($exception instanceof \Curl\Exception) {
            $message['detail'] = $exception->getContent();
        }
        $message['code'] = $exception->getCode();
        $message['message'] = $exception->getMessage();
        $message['file'] = $exception->getFile() . ' (' . $exception->getLine() . ')';
        $message['trace'] = $exception->getTraceAsString();

        return $message;
    }
}
