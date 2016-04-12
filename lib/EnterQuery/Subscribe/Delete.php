<?php

namespace EnterQuery\Subscribe
{
    use EnterQuery\Subscribe\Delete\Response;

    class Delete
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $email;
        /** @var string */
        public $type;
        /** @var string */
        public $channelId;
        /** @var string|null */
        public $userToken;
        /** @var Response */
        public $response;

        public function __construct($email = null, $type = null, $channelId = null, $userToken = null)
        {
            $this->response = new Response();

            $this->email = $email;
            $this->type = $type;
            $this->channelId = $channelId;
            $this->userToken = $userToken;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/subscribe/delete',
                    [
                        'token' => $this->userToken,
                    ]
                ),
                [
                    [
                        'email' => $this->email,
                        'channel_id' => $this->channelId,
                        'type' => $this->type,
                    ],
                ],
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

namespace EnterQuery\Subscribe\Delete
{
    class Response
    {
    }
}