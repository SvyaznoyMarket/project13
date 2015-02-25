<?php

namespace EnterQuery\Region
{

    use EnterQuery\Region\GetById\Response;

    class GetById {
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
         * @param callable|null $callback
         * @return $this
         */
        public function prepare(\Exception &$error = null, $callback = null)
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
                $callback,
                $error,
                function($response) {
                    $this->response->region = $this->decodeResponse($response)['result'][0];
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