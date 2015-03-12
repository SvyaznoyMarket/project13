<?php

namespace EnterQuery\Product\Line
{
    use EnterQuery\Product\Line\GetByTokenList\Response;

    class GetByTokenList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string[] */
        public $tokens = [];
        /** @var string|null */
        public $regionId;
        /** @var Response */
        public $response;

        public function __construct($tokens = [], $regionId = null)
        {
            $this->response = new Response();

            $this->tokens = $tokens;
            $this->regionId = $regionId;
        }

        /**
         * @param \Exception $error
         * @return $this
         */
        public function prepare(\Exception &$error = null)
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/line/list',
                    [
                        'token'  => $this->tokens,
                        'geo_id' => $this->regionId,
                    ]
                ),
                [], // data
                1, // timeout multiplier
                $error,
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->lines = isset($result[0]) ? $result : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\Line\GetByTokenList
{
    class Response
    {
        /** @var array */
        public $lines = [];
    }
}