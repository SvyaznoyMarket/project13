<?php

namespace EnterQuery\Product
{
    use EnterQuery\Product\GetDescriptionByIdList\Filter;
    use EnterQuery\Product\GetDescriptionByIdList\Response;

    class GetDescriptionByIdList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string[] */
        public $ids = [];
        /** @var Filter */
        public $filter;
        /** @var Response */
        public $response;

        public function __construct(array $ids = [], $filter = null)
        {
            $this->response = new Response();

            $this->ids = $ids;
            $this->filter = $filter ?: new Filter();
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'product/get-description/v1',
                    [
                        'ids'         => $this->ids,
                        'trustfactor' => $this->filter->trustfactor,
                        'category'    => $this->filter->category,
                        'seo'         => $this->filter->seo,
                        'media'       => $this->filter->media,
                        'property'    => $this->filter->property,
                        'label'       => $this->filter->label,
                        'brand'       => $this->filter->brand,
                        'tag'         => $this->filter->tag,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->products = (isset($result['products']) && is_array($result['products'])) ? array_values($result['products']) : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\GetDescriptionByIdList
{
    class Response
    {
        /** @var array */
        public $products = [];
    }

    class Filter
    {
        /** @var bool */
        public $trustfactor = false;
        /** @var bool */
        public $category = false;
        /** @var bool */
        public $seo = false;
        /** @var bool */
        public $media = false;
        /** @var bool */
        public $property = false;
        /** @var bool */
        public $label = false;
        /** @var bool */
        public $brand = false;
        /** @var bool */
        public $tag = false;
    }
}