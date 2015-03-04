<?php

namespace EnterQuery\AbTest
{
    use EnterQuery\AbTest\GetActive\Response;

    class GetActive
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var Response */
        public $response;

        public function __construct()
        {
            $this->response = new Response();
        }

        /**
         * @param \Exception $error
         * @param callable[] $callbacks
         * @return $this
         */
        public function prepare(\Exception &$error = null, array $callbacks = [])
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/ab_test/get-active'
                ),
                [], // data
                0.5, // timeout multiplier
                $callbacks,
                $error,
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->tests = isset($result[0]) ? $result : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\AbTest\GetActive
{
    class Response
    {
        /** @var array */
        public $tests = [];
    }
}