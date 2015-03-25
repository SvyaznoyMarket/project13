<?php

namespace EnterQuery\Product\Category
{
    use EnterQuery\Product\Category\GetAvailable\Response;

    class GetAvailable
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\SearchQueryTrait;

        /** @var Response */
        public $response;
        /** @var array */
        public $rootCriteria = [];
        /** @var int|null */
        public $depth;
        /** @var bool|null */
        public $loadParents;
        /** @var bool|null */
        public $loadSibling;
        /** @var array */
        public $filterData;

        public function __construct(
            array $rootCriteria = null,
            $depth = null,
            $loadParents = null,
            $loadSibling = null,
            array $filterData = []
        ) {
            $this->response = new Response();

            $this->rootCriteria = $rootCriteria;
            $this->depth = $depth;
            $this->loadParents = $loadParents;
            $this->loadSibling = $loadSibling;
            $this->filterData = $filterData;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $urlQuery = [];
            // критерий для корневой категории
            if (isset($this->rootCriteria['token'])) {
                $urlQuery['root_slug'] = $this->rootCriteria['token'];
            }
            if (isset($this->rootCriteria['id'])) {
                $urlQuery['root_id'] = $this->rootCriteria['id'];
            }
            // загружать предков относительно корневой категории
            if ($this->loadParents) {
                $urlQuery['load_parents'] = true;
            }
            // загружать соседей относительно корневой категории
            if ($this->loadSibling) {
                $urlQuery['load_siblings'] = true;
            }
            // глубина загрузки потомков относительно корневой категории
            if (is_int($this->depth)) {
                $urlQuery['depth'] = $this->depth;
            }
            // фильтры
            if ($this->filterData) {
                $urlQuery['filter'] = [
                    'filters' => $this->filterData,
                ];
            }

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'category/get-available',
                    $urlQuery
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->categories = isset($result[0]) ? $result : [];

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
        public $categories = [];
    }
}