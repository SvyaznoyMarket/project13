<?php

namespace EnterQuery\Product\Similar {
    use EnterQuery\Product\Similar\GetUiListByProductUi\Response;

    class GetUiListByProductUi {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\SearchQueryTrait;

        /** @var string */
        public $productUi;
        /** @var string|null */
        public $regionId;
        /** @var Response */
        public $response;

        public function __construct($productUi = null, $regionId = null) {
            $this->response = new Response();

            $this->productUi = $productUi;
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
                        'product_uid' => $this->productUi,
                        'region_id'   => $this->regionId,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    if (!empty($result['before']) && is_array($result['before'])) {
                        $this->response->beforeProductUis = array_column($result['before'], 'uid');
                    }

                    if (!empty($result['after']) && is_array($result['after'])) {
                        $this->response->afterProductUis = array_column($result['after'], 'uid');
                    }

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\Similar\GetUiListByProductUi {
    class Response {
        /** @var array */
        public $beforeProductUis = [];
        /** @var array */
        public $afterProductUis = [];
    }
}