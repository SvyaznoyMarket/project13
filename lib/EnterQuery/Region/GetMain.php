<?php

namespace EnterQuery\Region
{
    use EnterQuery\Region\GetMain\Response;

    class GetMain
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var Response */
        public $response;

        public function __construct()
        {
            $this->response = new Response();
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/geo/get-menu-cities',
                    []
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->regions = isset($result[0]) ? $result : [];

                    return $result; // for cache
                },
                0.5, // timeout ratio
                [0] // delay ratio
            );

            return $this;
        }
    }
}

namespace EnterQuery\Region\GetMain
{
    class Response
    {
        /** @var array */
        public $regions = [];
    }
}