<?php

namespace EnterQuery\Shop
{
    use EnterQuery\Shop\GetById\Response;

    class GetById
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string */
        public $ids;
        /** @var Response */
        public $response;

        public function __construct(array $ids = [])
        {
            $this->response = new Response();

            $this->ids = $ids;
        }

        /**
         * @param \Exception $error
         * @param callable|null $callback
         * @return $this
         */
        public function prepare(\Exception &$error = null, $callback = null)
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'shop/get',
                    [
                        'id' => $this->ids,
                    ]
                ),
                [], // data
                1, // timeout multiplier
                $callback,
                $error,
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

namespace EnterQuery\Shop\GetById
{
    class Response
    {
        /** @var array */
        public $shops = [];
    }
}