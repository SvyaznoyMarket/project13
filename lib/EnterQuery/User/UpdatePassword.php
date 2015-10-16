<?php

namespace EnterQuery\User
{
    use EnterQuery\User\UpdatePassword\Response;

    class UpdatePassword
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $token;
        /** @var string */
        public $password;
        /** @var string */
        public $newPassword;
        /** @var Response */
        public $response;

        public function __construct($token = null, $password = null, $newPassword = null)
        {
            $this->response = new Response();

            $this->token = $token;
            $this->password = $password;
            $this->newPassword = $newPassword;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/user/change-password',
                    [
                        'token'        => $this->token,
                        'password'     => $this->password,
                        'new_password' => $this->newPassword,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->confirmed = isset($result['confirmed']) ? (bool)$result['confirmed'] : null;

                    return $result; // for cache
                },
                3,
                [0]
            );

            return $this;
        }
    }
}

namespace EnterQuery\User\UpdatePassword
{
    class Response
    {
        /** @var bool|null */
        public $confirmed;
    }
}