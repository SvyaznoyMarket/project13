<?php

namespace EnterQuery\Shop
{
    use EnterQuery\Shop\GetByIdList\Response;

    class GetByIdList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string[] */
        public $ids;
        /** @var Response */
        public $response;

        public function __construct(array $ids = [])
        {
            $this->response = new Response();

            $this->ids = $ids;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'shop/get',
                    [
                        'id' => $this->ids,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->shops = isset($result[0]) ? $result : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Shop\GetByIdList
{
    class Response
    {
        /** @var array */
        public $shops = [];
    }
}