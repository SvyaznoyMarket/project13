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
         * @param \Exception $error
         * @param callable[] $callbacks
         * @return $this
         */
        public function prepare(\Exception &$error = null, array $callbacks = [])
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/subscribe/get-channel',
                    []
                ),
                [], // data
                0.5, // timeout multiplier
                $callbacks,
                $error,
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->channels = isset($result[0]) ? $result : [];

                    return $result; // for cache
                }
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