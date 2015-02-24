<?php

namespace EnterQuery\Product {

    use EnterQuery\Product\GetByToken\Response;

    class GetByToken {
        use \EnterQuery\CurlQueryTrait, \EnterQuery\JsonTrait;

        /** @var string */
        public $token;
        /** @var string|null */
        public $regionId;
        /** @var Response */
        public $response;

        public function __construct($token, $regionId = null)
        {
            $this->response = new Response();

            $this->token = $token;
            $this->regionId = $regionId;
        }

        public function prepare($callback = null, \Exception &$error = null)
        {
            $config = (array)\App::config()->coreV2 + [
                'url'       => null,
                'timeout'   => null,
                'client_id' => null,
            ];

            $this->pushCurlQuery(
                $this->buildUrl(
                    preg_replace('/\/v2\/$/', '/', $config['url']),
                    '/v2/product/get',
                    [
                        'select_type' => 'slug',
                        'slug'        => $this->token,
                        'geo_id'      => $this->regionId,
                    ]
                ),
                [], // data
                $config['timeout'] * 1000 * 1, // timeout
                $callback,
                $error,
                [$this, 'decode']
            );
        }

        private function decode($response)
        {
            $this->response->products = $this->jsonToArray($response)['result'];
        }
    }
}

namespace EnterQuery\Product\GetByToken {
    class Response
    {
        /** @var array */
        public $products = [];
    }
}