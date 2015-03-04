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
         * @param \Exception $error
         * @param callable[] $callbacks
         * @return $this
         */
        public function prepare(\Exception &$error = null, array $callbacks = [])
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/user/get',
                    [
                        'token' => $this->token,
                    ]
                ),
                [], // data
                1, // timeout multiplier
                $callbacks,
                $error,
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