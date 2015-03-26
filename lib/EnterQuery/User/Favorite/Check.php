<?php

namespace EnterQuery\User\Favorite
{
    use EnterQuery\User\Favorite\Check\Response;

    class Check
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $userUi;
        /** @var string[] */
        public $uis = [];
        /** @var Response */
        public $response;

        /**
         * @param string|null $userUi
         * @param string[]|null $uis
         */
        public function __construct($userUi = null, array $uis = [])
        {
            $this->response = new Response();

            $this->userUi = $userUi;
            $this->uis = $uis;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/favorite/check',
                    [
                        'user_uid' => $this->userUi,
                        'products' => $this->uis,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->products = isset($result['products'][0]) ? $result['products'] : [];

                    return $result; // for cache
                },
                0.4
            );

            return $this;
        }
    }
}

namespace EnterQuery\User\Favorite\Check
{
    class Response
    {
        /** @var array */
        public $products = [];
    }
}