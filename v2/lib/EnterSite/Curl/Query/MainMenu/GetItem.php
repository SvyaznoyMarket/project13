<?php

namespace EnterSite\Curl\Query\MainMenu;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CmsQueryTrait;
use EnterSite\Curl\Query\Url;

class GetItem extends Query {
    use CmsQueryTrait;

    /** @var array */
    protected $result;

    public function __construct() {
        $this->url = new Url();
        $this->url->path = 'v2/main-menu.json';

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = isset($data['items'][0]) ? $data : ['items' => []];
    }
}