<?php

namespace EnterQuery\Product {
    use EnterLab\Query;
    use EnterLab\Curl;
    use EnterQuery\CoreQueryTrait;
    use EnterQuery\Product\GetByToken\Parameter;
    use EnterQuery\Product\GetByToken\Result;

    class GetByToken extends Query\Query implements Query\CurlQueryInterface
    {
        use CoreQueryTrait;

        /** @var Parameter */
        public $parameter;
        /** @var Result */
        private $result;

        public function __construct()
        {
            $this->result = new Result();
            $this->parameter = new Parameter();
        }

        /**
         * @return Curl\Request
         * @throws \Exception
         */
        public function getRequest()
        {
            if (!$this->parameter->token) {
                throw new \Exception('Не задан параметр token');
            }
            if (!$this->parameter->regionId) {
                throw new \Exception('Не задан параметр regionId');
            }

            $request = $this->createRequest(
                '/v2/product/get',
                [
                    'select_type' => 'slug',
                    'slug'        => $this->parameter->token,
                    'geo_id'      => $this->parameter->regionId,
                ],
                []
            );

            return $request;
        }

        public function parseResponse($response)
        {

        }
    }
}

namespace EnterQuery\Product\GetByToken {
    class Parameter
    {
        /** @var string */
        public $token;
        /** @var string */
        public $regionId;
    }

    class Result {
        /** @var array */
        public $products = [];
    }
}