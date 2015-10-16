<?php

namespace EnterQuery\Event
{
    use EnterQuery\Event\PushCartState\Response;

    class PushCartState
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\EventQueryTrait;

        /** @var array */
        public $data;
        /** @var Response */
        public $response;

        public function __construct($data = [])
        {
            $this->response = new Response();

            $this->data = $data;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $config = (array)\App::config()->eventService + [
                'url'       => null,
                'client_id' => null,
                'timeout'   => null,
            ];

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'cart/state',
                    []
                ),
                array_merge([
                    'client_id' => $config['client_id'],
                ], $this->data), // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    return $result; // for cache
                },
                0.05, // timeout ratio
                [0] // delay ratio
            );

            return $this;
        }
    }
}

namespace EnterQuery\Event\PushCartState
{
    class Response
    {
    }
}