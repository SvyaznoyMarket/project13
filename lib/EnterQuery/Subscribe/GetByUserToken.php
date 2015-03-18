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
         * @return $this
         */
        public function prepare(\Exception &$error = null)
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/subscribe/get',
                    [
                        'token' => $this->userToken,
                    ]
                ),
                [], // data
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