<?php

namespace EnterQuery\Config
{
    use EnterQuery\Config\GetByKeys\Response;

    class GetByKeys
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var array */
        public $keys = [];
        /** @var Response */
        public $response;

        public function __construct($keys = [])
        {
            $this->response = new Response();

            $this->keys = $keys;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/parameter/get-by-keys',
                    [
                        'keys' => $this->keys,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->keys = is_array($result) ? $result : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Config\GetByKeys
{
    class Response
    {
        /** @var array */
        public $keys = [];
    }
}