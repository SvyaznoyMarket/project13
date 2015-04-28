<?php

namespace EnterQuery\Event
{
    use EnterQuery\Event\PushProductView\Response;

    class PushProductView
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\EventQueryTrait;

        /** @var string */
        public $productUi;
        /** @var string|null */
        public $userUi;
        /** @var Response */
        public $response;

        public function __construct($productUi = null, $userUi = null)
        {
            $this->response = new Response();

            $this->productUi = $productUi;
            $this->userUi = $userUi;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'product/view',
                    []
                ),
                [
                    'product_uid' => $this->productUi,
                    'user_uid'    => $this->userUi,
                ], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Event\PushProductView
{
    class Response
    {
    }
}