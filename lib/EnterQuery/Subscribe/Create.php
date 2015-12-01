<?php

namespace EnterQuery\Subscribe
{
    use EnterQuery\Subscribe\Create\Response;

    class Create
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $email;
        /** @var string */
        public $channelId;
        /** @var string|null */
        public $userToken;
        /** @var string */
        public $gaClientId = '';
        /** @var Response */
        public $response;

        public function __construct($email = null, $channelId = null, $userToken = null, $gaClientId = '')
        {
            $this->response = new Response();

            $this->email = $email;
            $this->channelId = $channelId;
            $this->userToken = $userToken;
            $this->gaClientId = $gaClientId;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $queryParams = [
                'email'      => $this->email,
                'channel_id' => $this->channelId,
            ];
            if ($this->userToken) {
                $queryParams['token'] = $this->userToken;
            }
            if ($this->gaClientId) {
                $queryParams['ga_client_id'] = $this->gaClientId;
            }

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/subscribe/create',
                    $queryParams
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    //$this->response->;

                    return $result; // for cache
                },
                1, // timeout ratio
                [0] // delay ratio
            );

            return $this;
        }
    }
}

namespace EnterQuery\Subscribe\Create
{
    class Response
    {
    }
}