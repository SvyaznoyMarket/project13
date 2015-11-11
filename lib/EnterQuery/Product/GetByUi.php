<?php

namespace EnterQuery\Product
{
    use EnterQuery\Product\GetByUi\Response;

    class GetByUi
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $ui;
        /** @var string|null */
        public $regionId;
        /** @var Response */
        public $response;

        public function __construct($ui = null, $regionId = null)
        {
            $this->response = new Response();

            $this->ui = $ui;
            $this->regionId = $regionId;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/product/get-v3',
                    [
                        'ui'          => [$this->ui],
                        'geo_id'      => $this->regionId,
                        'withModels'  => 0,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->product = $result[0];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\GetByUi
{
    class Response
    {
        /** @var array|null */
        public $product;
    }
}