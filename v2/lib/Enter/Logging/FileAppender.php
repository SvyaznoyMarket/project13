<?php

namespace Enter\Logging;

class FileAppender implements AppenderInterface {
    /** @var string */
    private $file;

    /**
     * @param string $file
     */
    public function __construct($file) {
        $this->file = $file;
    }

    /**
     * @param $messages
     */
    public function dump(array $messages) {
        foreach ($messages as &$message) {
            $message = json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        if (isset($message)) unset($message);

        file_put_contents($this->file, PHP_EOL . implode(PHP_EOL, $messages) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}