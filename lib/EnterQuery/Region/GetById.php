<?php

namespace EnterQuery\Region
{
    use EnterQuery\Region\GetById\Response;

    class GetById
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

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
         * @param \Exception $error
         * @param callable[] $callbacks
         * @return $this
         */
        public function prepare(\Exception &$error = null, array $callbacks = [])
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/geo/get',
                    [
                        'id' => [$this->id],
                    ]
                ),
                [], // data
                1, // timeout multiplier
                $callbacks,
                $error,
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