<?php

namespace EnterQuery\User
{
    use EnterQuery\User\GetByToken\Response;

    class GetByToken
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $token;
        /** @var Response */
        public $response;

        public function __construct($token = null)
        {
            $this->response = new Response();

            $this->token = $token;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/user/get',
                    [
                        'token' => $this->token,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->user = isset($result['id']) ? $result : null;

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\User\GetByToken
{
    class Response
    {
        /** @var array|null */
        public $user;
    }
}