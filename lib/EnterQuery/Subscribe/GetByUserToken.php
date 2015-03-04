<?php

namespace EnterQuery\Subscribe
{
    use EnterQuery\Subscribe\GetByUserToken\Response;

    class GetByUserToken
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $userToken;
        /** @var Response */
        public $response;

        public function __construct($userToken = null)
        {
            $this->response = new Response();

            $this->userToken = $userToken;
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
                    'v2/subscribe/get',
                    [
                        'token' => $this->userToken,
                    ]
                ),
                [], // data
                0.5, // timeout multiplier
                $callbacks,
                $error,
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->subscribes = isset($result[0]) ? $result : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Subscribe\GetByUserToken
{
    class Response
    {
        /** @var array */
        public $subscribes = [];
    }
}