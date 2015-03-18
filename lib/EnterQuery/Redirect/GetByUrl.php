<?php

namespace EnterQuery\Redirect
{
    use EnterQuery\Redirect\GetByUrl\Response;

    class GetByUrl
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string */
        public $fromUrl;
        /** @var Response */
        public $response;

        public function __construct($fromUrl = null)
        {
            $this->response = new Response();

            $this->fromUrl = $fromUrl;
        }

        /**
         * @param \Exception $error
         * @return $this
         */
        public function prepare(\Exception &$error = null)
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'seo/redirect',
                    [
                        'from_url' => $this->fromUrl,
                    ]
                ),
                [], // data
                $error,
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->toUrl = $result['to_url'];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Redirect\GetByUrl
{
    class Response
    {
        /** @var string|null */
        public $toUrl;
        /** @var string */
        //public $reason;
    }
}