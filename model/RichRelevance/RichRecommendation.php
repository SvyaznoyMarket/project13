<?php

namespace Model\RichRelevance;

use Model\Product\RichRelevanceProduct;

class RichRecommendation
{
    /** @var RichRelevanceProduct[] */
    public $products = [];
    /** @var string */
    public $placementType;
    /** @var string */
    public $placement;
    /** @var string */
    public $message;

    public function __construct($data = [])
    {
        if (array_key_exists('recommendedProducts', $data) && is_array($data['recommendedProducts'])) {
            $this->products = array_map(
                function ($item) {
                    return new RichRelevanceProduct($item);
                },
                $data['recommendedProducts']
            );
        }

        if (array_key_exists('placementType', $data)) {
            $this->placementType = $data['placementType'];
        }

        if (array_key_exists('placement', $data)) {
            $this->placement = $data['placement'];
        }

        if (array_key_exists('strategyMessage', $data)) {
            $this->message = $data['strategyMessage'];
        }
    }

    public function getProductIds()
    {
        return array_map(
            function (RichRelevanceProduct $product) {
                return $product->id;
            },
            $this->products
        );
    }

    /**
     * @return array
     */
    public function getProductsById()
    {
        $result = [];

        foreach ($this->products as $product)
        {
            $result[$product->id] = $product;
        }

        return $result;
    }

    public function replaceProducts(array $products)
    {
        foreach ($this->products as &$product) {
            if (isset($products[$product->id])) $product = $products[$product->id];
        }
    }
}
