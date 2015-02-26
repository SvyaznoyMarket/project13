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
         * @param \Exception $error
         * @param callable|null $callback
         * @return $this
         */
        public function prepare(\Exception &$error = null, $callback = null)
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
                1, // timeout multiplier
                $callback,
                $error,
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->categories = isset($result['result'][0]) ? $result['result'] : [];
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