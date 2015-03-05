<?php

namespace EnterQuery\Product
{
    use EnterQuery\Product\GetByIdList\Response;

    class GetByIdList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string[] */
        public $ids = [];
        /** @var string|null */
        public $regionId;
        /** @var Response */
        public $response;

        public function __construct($ids = null, $regionId = null)
        {
            $this->response = new Response();

            $this->ids = $ids;
            $this->regionId = $regionId;
        }

        /**
         * @param \Exception $error
         * @param callable[] $callbacks
         * @return $this
         */
        public function prepare(\Exception &$error = null, array $callbacks = [])
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/product/get',
                    [
                        'select_type' => 'id',
                        'id'          => $this->ids,
                        'geo_id'      => $this->regionId,
                    ]
                ),
                [], // data
                1, // timeout multiplier
                $callbacks,
                $error,
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

namespace EnterQuery\Product\GetByIdList
{
    class Response
    {
        /** @var array|null */
        public $product;
    }
}