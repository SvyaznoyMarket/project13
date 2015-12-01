<?php

namespace EnterQuery\Product
{
    use EnterQuery\Product\GetByBarcode\Response;

    class GetByBarcode
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $barcode;
        /** @var string|null */
        public $regionId;
        /** @var Response */
        public $response;

        public function __construct($barcode = null, $regionId = null)
        {
            $this->response = new Response();

            $this->barcode = $barcode;
            $this->regionId = $regionId;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/product/get-v3',
                    [
                        'select_type' => 'bar_code',
                        'bar_code'    => $this->barcode,
                        'geo_id'      => $this->regionId,
                        'withModels'  => 0,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->product = isset($result[0]) ? $result[0] : null;

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\GetByBarcode
{
    class Response
    {
        /** @var array|null */
        public $product;
    }
}