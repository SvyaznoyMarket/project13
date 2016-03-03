<?php

namespace EnterQuery\UserCallback
{
    use EnterQuery\UserCallback\Create\Response;

    class Create
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $phone;
        /**
         * Время после полуночи, с которого можно звонить клиенту [секунды]
         * @var int
         */
        public $from;
        /**
         * Время после полуночи, до которого можно звонить клиенту [секунды]
         * @var int
         */
        public $to;
        /** @var Response */
        public $response;

        public function __construct($phone = null, $from = null, $to = null)
        {
            $this->response = new Response();

            $this->phone = $phone;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/callback/create',
                    []
                ),
                [
                    'mobile' => $this->phone,
                    'from' => $this->from,
                    'to' => $this->to,
                ], // data
                function($response, $curlQuery) {
                    $result = $this->decodeResponse($response, $curlQuery);

                    $this->response->confirmed = isset($result['confirm']) ? (bool)$result['confirm'] : null;

                    return $result; // for cache
                },
                2,
                [0] // delay ratio
            );

            return $this;
        }
    }
}

namespace EnterQuery\UserCallback\Create
{
    class Response
    {
        /** @var bool */
        public $confirmed;
    }
}