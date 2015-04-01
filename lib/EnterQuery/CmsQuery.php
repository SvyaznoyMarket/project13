<?php

namespace EnterQuery
{
    class CmsQuery
    {
        use CurlQueryTrait;
        use JsonTrait;

        /** @var string */
        public $path;
        /** @var array|null */
        public $response;

        /**
         * @param string|null $path
         */
        public function __construct($path = null)
        {
            $this->path = $path;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    $this->path,
                    []
                ),
                [], // data
                function($response, $statusCode) {
                    $this->response = $this->decodeResponse($response, $statusCode);

                    return $this->response; // for cache
                },
                0.2,
                [0]
            );

            return $this;
        }

        /**
         * @param string $action
         * @param string array $query
         * @return string
         */
        public function buildUrl($action, $query = [])
        {
            $config = (array)\App::config()->dataStore + [
                'url'       => null,
                'timeout'   => null,
            ];

            return
                $config['url']
                . $action
                . ($query ? ('?' . http_build_query($query)) : '')
            ;
        }

        /**
         * @param string|null $response
         * @param $statusCode
         * @return array
         * @throws \Exception
         */
        protected function decodeResponse(&$response, $statusCode)
        {
            $result = $this->jsonToArray($response);
            if ($statusCode >= 300) {
                throw new \Exception(sprintf('Invalid http code %s', $statusCode), (int)$statusCode);
            }

            return $result;
        }
    }
}