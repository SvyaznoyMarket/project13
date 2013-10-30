<?php

namespace Enter\Site\Curl\Query\Region;

use Enter\Curl\Query;
use Enter\Site\Curl\Query\CoreQueryTrait;

class GetItemById extends Query {
    use CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param $id
     */
    public function __construct($id) {
        $this->url = 'geo/get?' . http_build_query([
            'id' => $id,
        ]);

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = isset($data[0]['id']) ? $data[0] : null;
    }
}