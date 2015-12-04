<?php

namespace EnterQuery\User\Address
{
    use EnterQuery\User\Address\Get\Response;

    class Get
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $userUi;
        /** @var Response */
        public $response;

        /**
         * @param string|null $userUi
         */
        public function __construct($userUi = null)
        {
            $this->response = new Response();

            $this->userUi = $userUi;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/address',
                    [
                        'user_uid'       => $this->userUi,
                        'last_selection' => 'DESC',
                        'priority'       => 'DESC',
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->addresses = isset($result[0]) ? $result : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\User\Address\Get
{
    class Response
    {
        /** @var array */
        public $addresses = [];
    }
}