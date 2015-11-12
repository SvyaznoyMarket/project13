<?php

namespace EnterQuery\User\Address
{
    use EnterQuery\User\Address\Create\Response;

    class Create
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $userUi;
        /** @var string */
        public $data = [];
        /** @var Response */
        public $response;

        /**
         * @param string|null $userUi
         * @param array $data
         */
        public function __construct($userUi = null, $data = [])
        {
            $this->response = new Response();

            $this->userUi = $userUi;
            $this->data = $data;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $data = array_merge(['user_uid' => $this->userUi], $this->data);

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/address/create',
                    []
                ),
                $data, // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    return $result; // for cache
                },
                1, // timeout ratio
                [0] // delay ratio
            );

            return $this;
        }
    }
}

namespace EnterQuery\User\Address\Create
{
    class Response
    {
    }
}