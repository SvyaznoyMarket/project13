<?php

namespace EnterQuery\Region
{
    use EnterQuery\Region\GetByIp\Response;

    class GetByIp
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $ip;
        /** @var Response */
        public $response;

        public function __construct($ip = null)
        {
            $this->response = new Response();

            $this->ip = $ip;
        }

        /**
         * @param \Exception $error
         * @return $this
         */
        public function prepare(\Exception &$error = null)
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/geo/locate',
                    [
                        'ip' => $this->ip,
                    ]
                ),
                [], // data
                $error,
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->region = $result;

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Region\GetByIp
{
    class Response
    {
        /** @var array|null */
        public $region;
    }
}