<?php

namespace EnterQuery\Product\Next {
    use EnterQuery\Product\Next\GetByUi\Response;

    class GetByUi {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\SearchQueryTrait;

        /** @var string */
        public $ui;
        /** @var string|null */
        public $regionId;
        /** @var Response */
        public $response;

        public function __construct($ui = null, $regionId = null) {
            $this->response = new Response();

            $this->ui = $ui;
            $this->regionId = $regionId;
        }

        /**
         * @return $this
         */
        public function prepare() {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'listing/next',
                    [
                        'product_uid' => $this->ui,
                        'region_id'   => $this->regionId,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->beforeProductUis = array_map(function($item) { return $item['uid']; }, $result['before']);
                    $this->response->afterProductUis = array_map(function($item) { return $item['uid']; }, $result['after']);

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\Next\GetByUi {
    class Response {
        /** @var array */
        public $beforeProductUis = [];
        /** @var array */
        public $afterProductUis = [];
    }
}