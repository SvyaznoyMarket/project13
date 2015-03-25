<?php

namespace EnterQuery\Brand
{
    use EnterQuery\Brand\GetByIdList\Response;

    class GetByIdList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string[] */
        public $ids = [];
        /** @var string|null */
        public $regionId;
        /** @var Response */
        public $response;

        public function __construct($ids = null, $regionId = null)
        {
            $this->response = new Response();

            $this->ids = $ids;
            $this->regionId = $regionId;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/brand/get',
                    [
                        'id'     => implode(',', $this->ids), // согласно https://wiki.enter.ru/pages/viewpage.action?pageId=8093924
                        'geo_id' => $this->regionId,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->brands = isset($result[0]) ? $result : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Brand\GetByIdList
{
    class Response
    {
        /** @var array */
        public $brands = [];
    }
}