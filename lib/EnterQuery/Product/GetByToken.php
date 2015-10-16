<?php

namespace EnterQuery\Product
{
    use EnterQuery\Product\GetByToken\Response;

    class GetByToken
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

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
                    'v2/product/get-v3',
                    [
                        'select_type' => 'slug',
                        'slug'        => $this->token,
                        'geo_id'      => $this->regionId,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->product = isset($result[0]) ? $result[0] : null;

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\GetByToken
{
    class Response
    {
        /** @var array|null */
        public $product;
    }
}