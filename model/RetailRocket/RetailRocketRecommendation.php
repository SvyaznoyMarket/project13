<?php

namespace Model\RetailRocket;

use Model\Product\Entity as Product;
use Model\Recommendation\RecommendationInterface;

class RetailRocketRecommendation implements RecommendationInterface
{
    /** @var Product[] */
    private $products = [];
    /** @var string */
    private $placement;
    /** @var string */
    private $message;

    public function __construct(array $data = [])
    {
        if (array_key_exists('products', $data) && is_array($data['products'])) {
            $this->products = array_map(function ($id) { return new Product(['id' => $id]); }, $data['products']);
        }

        if (array_key_exists('placement', $data)) {
            $this->placement = (string) $data['placement'];
        }

        if (array_key_exists('message', $data)) {
            $this->message = (string) $data['message'];
        }
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getPlacement()
    {
        return $this->placement;
    }

    public function getProductIds()
    {
        return array_map(
            function (Product $product) {
                return $product->id;
            },
            $this->products
        );
    }

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

    public function getSenderName()
    {
        return 'retailrocket';
    }


}