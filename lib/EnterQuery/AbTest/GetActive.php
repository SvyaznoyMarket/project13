<?php

namespace EnterQuery\AbTest {

    use EnterQuery\AbTest\GetActive\Response;

    class GetActive {
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
         * @param callable|null $callback
         * @return $this
         */
        public function prepare(\Exception &$error = null, $callback = null)
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/ab_test/get-active'
                ),
                [], // data
                1, // timeout multiplier
                $callback,
                $error,
                function($response) {
                    $result = $this->decodeResponse($response)['result'];

                    $this->response->tests = isset($result[0]) ? $result : [];
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