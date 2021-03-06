<?php

namespace EnterQuery\Product\Category
{
    use EnterQuery\Product\Category\GetByUi\Response;

    class GetByUi
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

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
                    'category/get/v1',
                    [
                        'uid'    => $this->ui,
                        'geo_id' => $this->regionId,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->category = isset($result['id']) ? $result : null;

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\Category\GetByUi
{
    class Response
    {
        /** @var array|null */
        public $category;
    }
}