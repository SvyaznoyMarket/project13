<?php

namespace EnterQuery\Product\Filter
{
    use EnterQuery\Product\Filter\Get\Response;

    class Get
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\SearchQueryTrait;

        /** @var array */
        public $filterData = [];
        /** @var string|null */
        public $regionId;
        /** @var Response */
        public $response;

        public function __construct(array $filterData = [], $regionId = null)
        {
            $this->response = new Response();

            $this->filterData = $filterData;
            $this->regionId = $regionId;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'listing/filter',
                    [
                        'region_id' => $this->regionId,
                        'filter'    => [
                            'filters' => $this->filterData,
                        ],
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->filters = isset($result[0]) ? $result : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\Filter\Get
{
    class Response
    {
        /** @var array */
        public $filters = [];
    }
}