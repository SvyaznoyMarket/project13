<?php

namespace EnterQuery\Product
{
    use EnterQuery\Product\GetByIdList\Filter;
    use EnterQuery\Product\GetByIdList\Response;

    class GetByIdList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string[] */
        public $ids = [];
        /** @var string|null */
        public $regionId;
        /** @var Filter */
        public $filter;
        /** @var Response */
        public $response;

        public function __construct($ids = null, $regionId = null, $filter = null)
        {
            $this->response = new Response();

            $this->ids = $ids;
            $this->regionId = $regionId;
            $this->filter = $filter ?: new Filter();
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $params = [
                'select_type' => 'id',
                'id'          => $this->ids,
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

namespace EnterQuery\Product\GetByIdList
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