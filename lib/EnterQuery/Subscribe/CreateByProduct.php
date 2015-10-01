<?php

namespace EnterQuery\Subscribe
{
    use EnterQuery\Subscribe\CreateByProduct\Response;

    class CreateByProduct
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $email;
        /** @var string */
        public $channelId;
        /** @var string|null */
        public $userToken;
        /** @var string|null */
        public $regionId;
        /** @var string|null */
        public $productId;
        /** @var Response */
        public $response;

        public function __construct($email = null, $channelId = null, $userToken = null, $regionId = null, $productId = null)
        {
            $this->response = new Response();

            $this->email = $email;
            $this->channelId = $channelId;
            $this->userToken = $userToken;
            $this->regionId = $regionId;
            $this->productId = $productId;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $queryParams = [
                'email'      => $this->email,
                'channel_id' => $this->channelId,
                'geo_id'     => $this->regionId,
                'product_id' => $this->productId,
            ];
            if ($this->userToken) {
                $queryParams['token'] = $this->userToken;
            }

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/subscribe/create',
                    $queryParams
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    //$this->response->;

                    return $result; // for cache
                },
                1, // timeout ratio
                [0] // delay ratio
            );

            return $this;
        }
    }
}

namespace EnterQuery\Subscribe\CreateByProduct
{
    class Response
    {
    }
}