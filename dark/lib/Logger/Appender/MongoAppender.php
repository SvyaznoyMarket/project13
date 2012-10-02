<?php

namespace Logger\Appender;

class MongoAppender implements AppenderInterface {
    private $db;

    public function __construct(\MongoDB $db) {
        $this->db = $db;
    }

    public function dump($messages) {
        $this->db->log->batchInsert($messages);
    }
}
