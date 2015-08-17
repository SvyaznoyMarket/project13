<?php

namespace EnterQuery\Promo
{
    use EnterQuery\Promo\Get\Response;

    class Get
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string[] */
        public $tags = [];
        /** @var Response */
        public $response;

        public function __construct(array $tags = [])
        {
            $this->response = new Response();

            $this->tags = $tags;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/promo/get',
                    [
                        'tags' => $this->tags,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->promos = isset($result['result'][0]) ? $result['result'] : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Promo\Get
{
    class Response
    {
        /** @var array */
        public $promos = [];
    }
}