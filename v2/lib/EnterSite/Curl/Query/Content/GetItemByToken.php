<?php

namespace EnterSite\Curl\Query\Content;

use Enter\Curl\Query;
use EnterSite\Curl\Query\ContentQueryTrait;
use EnterSite\Curl\Query\Url;

class GetItemByToken extends Query {
    use ContentQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param $token
     */
    public function __construct($token) {
        $this->url = new Url();
        $this->url->path = $token;

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = isset($data['content']) ? $data : null;
    }
}