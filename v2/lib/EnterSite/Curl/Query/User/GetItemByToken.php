<?php

namespace EnterSite\Curl\Query\User;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;
use EnterSite\Curl\Query\Url;

class GetItemByToken extends Query {
    use CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param $token
     */
    public function __construct($token) {
        $this->url = new Url();
        $this->url->path = 'v2/user/get';
        $this->url->query = [
            'token' => $token,
        ];

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = isset($data['id']) ? $data : null;
    }
}