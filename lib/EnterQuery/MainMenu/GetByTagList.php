<?php

namespace EnterQuery\MainMenu
{
    use EnterQuery\MainMenu\GetByTagList\Response;

    class GetByTagList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var Response */
        public $response;
        /** @var string[] */
        public $tags = [];

        public function __construct(array $tags = [])
        {
            $this->response = new Response();

            $this->tags = $tags;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'seo/main-menu',
                    [
                        'tags' => $this->tags,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->items = isset($result['item'][0]) ? $result['item'] : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\MainMenu\GetByTagList
{
    class Response
    {
        /** @var array */
        public $items = [];
    }
}