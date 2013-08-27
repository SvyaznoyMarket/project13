<?php

namespace Logger\Appender;

class FileAppender implements AppenderInterface {
    private $file;

    public function __construct($file) {
        $this->file = $file;
    }

    public function dump($messages) {
        foreach ($messages as &$message) {
            $message = implode(' ', $message);
        } if (isset($message)) unset($message);

        file_put_contents($this->file, PHP_EOL . implode(PHP_EOL, $messages) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
