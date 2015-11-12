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
        public $data = [
            'kladrId'     => null,
            'regionId'    => null,
            'street'      => null,
            'streetType'  => null,
            'building'    => null,
            'apartment'   => null,
            'description' => null,
        ];
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
            $this->data = array_merge($this->data, $data);
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/address/create',
                    []
                ),
                [
                    'user_uid'    => $this->userUi,
                    'kladr_id'    => $this->data['kladrId'],
                    'geo_id'      => $this->data['regionId'],
                    'street'      => $this->data['street'],
                    'street_type' => $this->data['streetType'],
                    'building'    => $this->data['building'],
                    'apartment'   => $this->data['apartment'],
                    'description' => $this->data['description'],
                ], // data
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