<?php

namespace EnterQuery\Product\Model
{
    use \EnterQuery\Product\Model\GetByBarcodeList\Filter;
    use \EnterQuery\Product\Model\GetByBarcodeList\Response;

    class GetByBarcodeList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string[] */
        public $barcodes = [];
        /** @var string */
        public $regionId = '';
        /** @var Filter */
        public $filter;
        /** @var Response */
        public $response;

        public function __construct(array $barcodes, $regionId)
        {
            $this->barcodes = $barcodes;
            $this->regionId = $regionId;

            $this->filter = new Filter();
            $this->response = new Response();
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/product/get-models',
                    [
                        'barcodes' => $this->barcodes,
                        'geo_id'   => $this->regionId,
                    ]
                ),
                [],
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->products = (isset($result['products']) && is_array($result['products'])) ? array_values($result['products']) : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\Model\GetByBarcodeList
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