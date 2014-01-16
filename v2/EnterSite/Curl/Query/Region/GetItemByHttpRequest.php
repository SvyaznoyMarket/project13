<?php

namespace EnterSite\Curl\Query\Region;

use Enter\Curl\Query;
use Enter\Http\Request;
use EnterSite\ConfigTrait;
use EnterSite\Curl\Query\CoreQueryTrait;

class GetItemByHttpRequest extends Query {
    use \EnterSite\Curl\Query\CoreQueryTrait;
    //use ConfigTrait; // https://bugs.php.net/bug.php?id=63911
    use \EnterSite\ConfigTrait {
        ConfigTrait::getConfig insteadof CoreQueryTrait;
    }

    /** @var array|null */
    protected $result;

    /**
     * @param Request $httpRequest
     */
    public function __construct($httpRequest) {
        $config = $this->getConfig()->region;

        $this->url = 'geo/get?' . http_build_query([
            'id' => $httpRequest->cookie[$config->cookieName] ?: $config->defaultId,
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