<?php

namespace EnterQuery\Product\Category
{
    use EnterQuery\Product\Category\GetAvailable\Response;

    class GetAvailable
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\SearchQueryTrait;

        /** @var string|null */
        public $regionId;
        /** @var array */
        public $rootCriteria = [];
        /** @var array */
        public $filterData;
        /** @var array */
        public $depth;
        /** @var Response */
        public $response;

        public function __construct($regionId = null, $rootCriteria = [], $filterData = [], $depth = null)
        {
            $this->response = new Response();

            $this->regionId = $regionId;
            $this->rootCriteria = $rootCriteria;
            $this->filterData = $filterData;
            $this->depth = $depth;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $queryParams = [
                'is_load_parents' => false, // TODO
                'geo_id'          => $this->regionId,
            ];
            if (!empty($this->rootCriteria['id'])) {
                $queryParams['root_id'] = $this->rootCriteria['id'];
            } else if (!empty($this->rootCriteria['token'])) {
                $queryParams['root_slug'] = $this->rootCriteria['token'];
            }
            if (null !== $this->depth) {
                $queryParams['depth'] = $this->depth;
            }
            if ($this->filterData) {
                $queryParams['filter'] = [
                    'filters' => $this->filterData,
                ];
            }

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'category/get-available',
                    $queryParams
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->categories = isset($result[0]['id']) ? $result : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\Category\GetAvailable
{
    class Response
    {
        /** @var array */
        public $categories;
    }
}