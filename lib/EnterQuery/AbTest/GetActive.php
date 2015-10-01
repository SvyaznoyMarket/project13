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
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/ab_test/get-active',
                    ['tags' => ['site-web']]
                ),
                [], // data
                function($response, $curlQuery) {
                    $result = $this->decodeResponse($response, $curlQuery)['result'];

                    $this->response->tests = isset($result[0]) ? $result : [];

                    if (isset($result[0])) {
                        $tests = [];
                        foreach ($result as $item) {
                            if (empty($item['token'])) {
                                continue;
                            }

                            $tests[$item['token']] = $item;
                        }

                        \App::config()->abTest['tests'] = $tests;
                    }

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