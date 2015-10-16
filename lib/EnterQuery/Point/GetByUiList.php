<?php

namespace EnterQuery\Point
{
    use EnterQuery\Point\GetByUiList\Response;

    class GetByUiList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string[] */
        public $uis;
        /** @var Response */
        public $response;

        public function __construct(array $uis = [])
        {
            $this->response = new Response();

            $this->uis = $uis;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/point/get',
                    [
                        'uids' => $this->uis,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->points = isset($result[0]) ? $result : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Point\GetByUiList
{
    class Response
    {
        /** @var array */
        public $points = [];
    }
}