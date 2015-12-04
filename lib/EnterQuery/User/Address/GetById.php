<?php

namespace EnterQuery\User\Address
{
    use EnterQuery\User\Address\GetById\Response;

    class GetById
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $userUi;
        /** @var string */
        public $id;
        /** @var Response */
        public $response;

        /**
         * @param string|null $userUi
         * @param string|null $id
         */
        public function __construct($userUi = null, $id = null)
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
                        'user_uid' => $this->userUi,
                        'id'       => $this->id,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->address = isset($result['id']) ? $result : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\User\Address\GetById
{
    class Response
    {
        /** @var array */
        public $address = [];
    }
}