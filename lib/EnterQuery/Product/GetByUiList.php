<?php

namespace EnterQuery\Product
{
    use EnterQuery\Product\GetByUiList\Response;

    class GetByUiList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string[] */
        public $uis = [];
        /** @var string|null */
        public $regionId;
        /** @var Response */
        public $response;

        public function __construct($uis = null, $regionId = null)
        {
            $this->response = new Response();

            $this->uis = $uis;
            $this->regionId = $regionId;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/product/get',
                    [
                        'select_type' => 'ui',
                        'ui'          => $this->uis,
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

namespace EnterQuery\Product\GetByUiList
{
    class Response
    {
        /** @var array */
        public $products = [];
    }
}