<?php

namespace EnterQuery\Product
{
    use EnterQuery\Product\GetByBarcodeList\Filter;
    use EnterQuery\Product\GetByBarcodeList\Response;

    class GetByBarcodeList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string[] */
        public $barcodes = [];
        /** @var string|null */
        public $regionId;
        /** @var Filter */
        public $filter;
        /** @var Response */
        public $response;

        public function __construct(array $barcodes = [], $regionId = null, $filter = null)
        {
            $this->response = new Response();

            $this->barcodes = $barcodes;
            $this->regionId = $regionId;
            $this->filter = $filter ?: new Filter();
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $params = [
                'select_type' => 'bar_code',
                'bar_code'    => $this->barcodes,
                'geo_id'      => $this->regionId,
                'withModels'  => 0,
            ];

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/product/get-v3',
                    $params
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

namespace EnterQuery\Product\GetByBarcodeList
{
    class Response
    {
        /** @var array */
        public $products = [];
    }

    class Filter
    {
    }
}