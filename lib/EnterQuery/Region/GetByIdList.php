<?php

namespace EnterQuery\Region
{
    use EnterQuery\Region\GetByIdList\Response;

    class GetByIdList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string */
        public $ids = [];
        /** @var Response */
        public $response;

        public function __construct($ids = [])
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
                    'api/geo/get-town',
                    [
                        'id' => $this->ids,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->regions = $result;

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Region\GetByIdList
{
    class Response
    {
        /** @var array */
        public $regions = [];
    }
}