<?php

namespace EnterQuery\Product
{
    use EnterQuery\Product\GetDescriptionByUi\Response;

    class GetDescriptionByUi
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string */
        public $uis;
        /** @var Response */
        public $response;

        public function __construct(array $uis = [], $filter = null)
        {
            $this->response = new Response();

            $this->uis = $uis;
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
                    'product/get-description/v1',
                    [
                        'uids'        => $this->uis,
                        'trustfactor' => true, // TODO: filter
                        'seo'         => true, // TODO: filter
                        'media'       => true, // TODO: filter
                    ]
                ),
                [], // data
                1, // timeout multiplier
                $callback,
                $error,
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->products = (isset($result['products']) && is_array($result['products'])) ? $result['products'] : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\GetDescriptionByUi
{
    class Response
    {
        /** @var array */
        public $products = [];
    }
}