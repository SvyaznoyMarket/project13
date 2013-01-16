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

        file_put_contents($this->file, implode("\n", $messages) . "\n", FILE_APPEND | LOCK_EX);
    }
}
