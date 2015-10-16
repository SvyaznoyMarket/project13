<?php

namespace EnterQuery\Product
{
    use EnterQuery\Product\GetDescriptionByUiList\Filter;
    use EnterQuery\Product\GetDescriptionByUiList\Response;

    class GetDescriptionByUiList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string[] */
        public $uis = [];
        /** @var Filter */
        public $filter;
        /** @var Response */
        public $response;

        public function __construct(array $uis = [], $filter = null)
        {
            $this->response = new Response();

            $this->uis = $uis;
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
                        'uids'        => $this->uis,
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

namespace EnterQuery\Product\GetDescriptionByUiList
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