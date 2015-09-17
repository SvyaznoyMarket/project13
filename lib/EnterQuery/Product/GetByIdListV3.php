<?php

namespace EnterQuery\Product {
    use EnterQuery\Product\GetByIdListV3\Response;

    class GetByIdListV3 {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string[] */
        public $ids = [];
        /** @var string|null */
        public $regionId;
        /** @var Response */
        public $response;

        public function __construct($ids = null, $regionId = null) {
            $this->response = new Response();

            $this->ids = $ids;
            $this->regionId = $regionId;
        }

        /**
         * @return $this
         */
        public function prepare() {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/product/get-v3',
                    [
                        'select_type' => 'id',
                        'id'          => $this->ids,
                        'geo_id'      => $this->regionId,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->products = isset($result[0]) ? $result : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\GetByIdListV3 {
    class Response {
        /** @var array */
        public $products = [];
    }
}