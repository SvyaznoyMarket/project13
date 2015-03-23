<?php

namespace EnterQuery\Product\Category
{
    use EnterQuery\Product\Category\GetTree\Response;

    class GetTree
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var Response */
        public $response;
        /** @var array */
        public $rootCriteria = [];
        /** @var int|null */
        public $depth;
        /** @var bool|null */
        public $loadParents;
        /** @var bool|null */
        public $loadSibling;
        /** @var bool|null */
        public $loadMedia;
        /** @var string[] */
        public $mediaTypes = [];

        public function __construct(
            array $rootCriteria = null,
            $depth = null,
            $loadParents = null,
            $loadSibling = null,
            $loadMedia = null,
            array $mediaTypes = []
        ) {
            $this->response = new Response();

            $this->rootCriteria = $rootCriteria;
            $this->depth = $depth;
            $this->loadParents = $loadParents;
            $this->loadSibling = $loadSibling;
            $this->loadMedia = $loadMedia;
            $this->mediaTypes = $mediaTypes;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $urlQuery = [];
            // критерий для корневой категории
            if (isset($this->rootCriteria['token'])) {
                $urlQuery['root_slug'] = $this->rootCriteria['token'];
            }
            if (isset($this->rootCriteria['id'])) {
                $urlQuery['root_id'] = $this->rootCriteria['id'];
            }
            if (isset($this->rootCriteria['ui'])) {
                $urlQuery['root_uid'] = $this->rootCriteria['ui'];
            }
            // загружать предков относительно корневой категории
            if ($this->loadParents) {
                $urlQuery['load_parents'] = true;
            }
            // загружать соседей относительно корневой категории
            if ($this->loadSibling) {
                $urlQuery['load_siblings'] = true;
            }
            // глубина загрузки потомков относительно корневой категории
            if (is_int($this->depth)) {
                $urlQuery['depth'] = $this->depth;
            }
            // media
            if ($this->loadMedia) {
                $urlQuery['load_medias'] = true;
            }
            // тип media
            if ($this->mediaTypes) {
                $urlQuery['media_types'] = $this->mediaTypes;
            }

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/category/tree',
                    $urlQuery
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->categories = isset($result[0]) ? $result : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\Category\GetTree
{
    class Response
    {
        /** @var array */
        public $categories = [];
    }
}