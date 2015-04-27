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
        /** @var string */
        public $brandToken;
        /** @var Response */
        public $response;

        /**
         * @param string|null $token
         * @param string|null $regionId
         * @param string|null $brandToken
         */
        public function __construct($token = null, $regionId = null, $brandToken = null)
        {
            $this->response = new Response();

            $this->token = $token;
            $this->regionId = $regionId;
            $this->brandToken = $brandToken;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $urlQuery =                     [
                'slug'   => $this->token,
                'geo_id' => $this->regionId,
            ];
            if ($this->brandToken) {
                $urlQuery['brand_slug'] = $this->brandToken;
            }

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'category/get/v1',
                    $urlQuery
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