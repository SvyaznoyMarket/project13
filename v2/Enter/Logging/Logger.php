<?php

namespace Enter\Logging;

class Logger implements LoggerInterface {
    /** @var AppenderInterface[] */
    private $appenders = [];
    /** @var array */
    private $messages = [];
    /** @var array */
    private $types = [];
    /** @var array */
    private $parameters = [];

    /**
     * @param AppenderInterface[] $appenders
     * @param array $types
     * @param array $parameters
     */
    public function __construct(array $appenders, array $types = null, array $parameters = []) {
        $this->appenders = $appenders;
        $this->types = $types;
        $this->parameters = $parameters;
    }

    /**
     * @param array $message
     * @return void
     */
    public function push(array $message) {
        if (!is_array($message)) {
            $message = ['message' => $message];
        }

        $time = time();
        $message = $this->parameters
            + [
                'time' => $time,
                'date' => date('M d H:i:s', $time),
                'type' => isset($message['type']) ? $message['type'] : 'info',
            ]
            + $message;

        if (null === $this->types || in_array($message['type'], $this->types)) {
            $this->messages[] = $message;
        }
    }

    public function dump() {
        foreach ($this->appenders as $appender) {
            $appender->dump($this->messages);
        }
    }
}