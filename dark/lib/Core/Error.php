<?php

namespace Core;

class Error {
    public $code;
    public $message;
    public $detail;

    public function __construct(array $data = array()) {
        if (array_key_exists('code', $data)) $this->code = $data['code'];
        if (array_key_exists('message', $data)) $this->message = $data['message'];
        if (array_key_exists('detail', $data)) $this->detail = $data['detail'];
    }
}