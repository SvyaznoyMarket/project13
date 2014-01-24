<?php

namespace EnterSite\Curl\Query\MainMenu;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CmsQueryTrait;

class GetList extends Query {
    use CmsQueryTrait;

    /** @var array */
    protected $result;

    public function __construct() {
        $this->url = 'main-menu.json';

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = isset($data['item'][0]) ? $data : [];
    }
}