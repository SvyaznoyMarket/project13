<?php

namespace EnterQuery\Product\Category
{
    use EnterQuery\Product\Category\GetRootTree\Response;

    /**
     * @deprecated
     */
    class GetRootTree
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\SearchQueryTrait;

        /** @var Response */
        public $response;
        /** @var string */
        public $regionId;
        /** @var int|null */
        public $maxLevel;

        public function __construct($regionId = null, $maxLevel = null) {
            $this->response = new Response();

            $this->regionId = $regionId;
            $this->maxLevel = $maxLevel;
        }

        /**
         * @deprecated
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'category/tree',
                    [
                        'region_id'       => $this->regionId,
                        'max_level'       => $this->maxLevel,
                        'is_load_parents' => true, // TODO: false?
                        'count_local'     => false,
                    ]
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

namespace EnterQuery\Product\Category\GetRootTree
{
    class Response
    {
        /** @var array */
        public $categories = [];
    }
}