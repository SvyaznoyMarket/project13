<?php

namespace Enter\Http;

class Request {
    /** @var Bag */
    public $query = [];
    /** @var Bag */
    public $data = [];
    /** @var CookieBag[] */
    public $cookie = [];
    /** @var FileBag[] */
    public $file = [];
    /** @var Bag[] */
    public $server = [];

    public function __construct($query = [], $data = [], $cookie = [], $file = [], $server = []) {
        $this->query = new Bag($query);
        $this->data = new Bag($data);
        $this->cookie = new CookieBag($cookie);
        $this->file = new FileBag($file);
        $this->server = new Bag($server);
    }
}