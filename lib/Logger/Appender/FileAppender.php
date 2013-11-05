<?php

namespace Logger\Appender;

class FileAppender implements AppenderInterface {
    private $file;
    private $pretty;

    public function __construct($file, $pretty = false) {
        $this->file = $file;
        $this->pretty = $pretty;
    }

    public function dump($messages) {
        if ($this->pretty) {
            foreach ($messages as &$message) {
                $message = json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            }
        } else {
            foreach ($messages as &$message) {
                $message = json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        if (isset($message)) unset($message);

        file_put_contents($this->file, PHP_EOL . implode(PHP_EOL, $messages) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
