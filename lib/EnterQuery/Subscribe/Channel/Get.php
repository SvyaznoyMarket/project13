<?php

namespace EnterQuery\Subscribe\Channel
{
    use EnterQuery\Subscribe\Channel\Get\Response;

    class Get
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

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
                    'v2/subscribe/get-channel',
                    []
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->channels = isset($result[0]) ? $result : [];

                    return $result; // for cache
                },
                0.5, // timeout ratio
                [0] // delay ratio
            );

            return $this;
        }
    }
}

namespace EnterQuery\Subscribe\Channel\Get
{
    class Response
    {
        /** @var array */
        public $channels = [];
    }
}