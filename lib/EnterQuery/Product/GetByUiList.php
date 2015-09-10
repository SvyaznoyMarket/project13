<?php

namespace EnterQuery\Product
{
    use EnterQuery\Product\GetByUiList\Filter;
    use EnterQuery\Product\GetByUiList\Response;

    class GetByUiList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string[] */
        public $uis = [];
        /** @var string|null */
        public $regionId;
        /** @var Filter */
        public $filter;
        /** @var Response */
        public $response;

        public function __construct($uis = null, $regionId = null, $filter = null)
        {
            $this->response = new Response();

            $this->uis = $uis;
            $this->regionId = $regionId;
            $this->filter = $filter ?: new Filter();
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $params = [
                'select_type' => 'ui',
                'ui'          => $this->uis,
                'geo_id'      => $this->regionId,
            ];

            if (!$this->filter->model) {
                $params['withModels'] = 0;
            }

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

namespace EnterQuery\Product\GetByUiList
{
    class Response
    {
        /** @var array */
        public $products = [];
    }

    class Filter
    {
        /** @var bool */
        public $model = true;
    }
}