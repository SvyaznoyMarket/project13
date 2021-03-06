<?php

namespace EnterQuery\Region
{
    use EnterQuery\Region\GetById\Response;

    class GetById
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string */
        public $id;
        /** @var Response */
        public $response;

        public function __construct($id = null)
        {
            $this->response = new Response();

            $this->id = $id;
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
                        'id' => [$this->id],
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->region = $result[0];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Region\GetById
{
    class Response
    {
        /** @var array|null */
        public $region;
    }
}