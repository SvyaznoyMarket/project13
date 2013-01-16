<?php

namespace Logger\Appender;

class EchoAppender implements AppenderInterface {
    public function dump($messages) {
        foreach ($messages as &$message) {
            $message = implode(' ', $message);
        } if (isset($message)) unset($message);

        echo '<pre>' . implode("\n", $messages) . '</pre>';
    }
}
