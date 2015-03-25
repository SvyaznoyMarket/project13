<?php

namespace EnterQuery\Product\Category
{
    use EnterQuery\Product\Category\GetByToken\Response;

    class GetByToken
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string */
        public $token;
        /** @var string|null */
        public $regionId;
        /** @var Response */
        public $response;

        public function __construct($token = null, $regionId = null)
        {
            $this->response = new Response();

            $this->token = $token;
            $this->regionId = $regionId;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'category/get/v1',
                    [
                        'slug'   => $this->token,
                        'geo_id' => $this->regionId,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->category = isset($result['id']) ? $result : null;

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\Category\GetByToken
{
    class Response
    {
        /** @var array|null */
        public $category;
    }
}