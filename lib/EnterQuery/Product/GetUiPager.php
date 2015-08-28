<?php

namespace EnterQuery\Product
{
    use EnterQuery\Product\GetUiPager\Filter;
    use EnterQuery\Product\GetUiPager\Sorting;
    use EnterQuery\Product\GetUiPager\Response;

    class GetUiPager
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\SearchQueryTrait;

        /** @var string|null */
        public $regionId;
        /** @var Filter */
        public $filter;
        /** @var Sorting */
        public $sorting;
        /** @var int */
        public $offset;
        /** @var int */
        public $limit;
        /** @var Response */
        public $response;

        public function __construct($regionId = null, $filter = null, $sorting = null, $offset = null, $limit = null)
        {
            $this->response = new Response();

            $this->regionId = $regionId;
            $this->filter = $filter ?: new Filter();
            $this->sorting = $sorting ?: new Sorting();
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $params = [
                'product_uid' => 1,
                'filter'      => [
                    'filters' => $this->filter->data,
                    'sort'    => $this->sorting->data,
                    'offset'  => $this->offset,
                    'limit'   => $this->limit,
                ],
            ];

            if ($this->regionId) {
                $params['region_id'] = $this->regionId;
            }

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/listing/list',
                    $params
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->uids = isset($result['list'][0]) ? $result['list'] : [];
                    $this->response->count = isset($result['count']) ? (int)$result['count'] : null;

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\GetUiPager
{
    class Response
    {
        /** @var string[] */
        public $uids = [];
        /** @var int|null */
        public $count;
    }

    class Filter
    {
        /** @var array */
        public $data = [];
    }

    class Sorting
    {
        /** @var array */
        public $data = [];
    }
}