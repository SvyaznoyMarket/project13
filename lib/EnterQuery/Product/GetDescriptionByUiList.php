<?php

namespace EnterQuery\Product
{
    use EnterQuery\Product\GetDescriptionByUiList\Response;

    class GetDescriptionByUiList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string[] */
        public $uis = [];
        /** @var Response */
        public $response;

        public function __construct(array $uis = [], $filter = null)
        {
            $this->response = new Response();

            $this->uis = $uis;
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
                $callbacks,
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

namespace EnterQuery\Product\GetDescriptionByUiList
{
    class Response
    {
        /** @var array */
        public $products = [];
    }
}