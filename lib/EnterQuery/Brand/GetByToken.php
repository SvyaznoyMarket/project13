<?php

namespace EnterQuery\Brand
{
    use EnterQuery\Brand\GetByToken\Response;

    class GetByToken
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string[] */
        public $token = [];
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
                    'v2/brand/get',
                    [
                        'token'  => $this->token,
                        'geo_id' => $this->regionId,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->brand = isset($result[0]['id']) ? $result[0] : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Brand\GetByToken
{
    class Response
    {
        /** @var array|null */
        public $brand;
    }
}