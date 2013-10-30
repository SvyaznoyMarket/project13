<?php

namespace Enter\Site\Curl\Query\Region;

use Enter\Curl\Query;
use Enter\Http\Request;
use Enter\Site\Curl\Query\CoreQueryTrait;

class GetItemByHttpRequest extends Query {
    use CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param Request $httpRequest
     * @param $defaultId
     */
    public function __construct($httpRequest, $defaultId) {
        $this->url = 'geo/get?' . http_build_query([
            'id' => $httpRequest->cookie['geo_id'] ?: $defaultId,
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