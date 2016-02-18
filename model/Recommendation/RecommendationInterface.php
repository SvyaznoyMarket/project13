<?php

namespace Model\Recommendation;


interface RecommendationInterface
{
    public function getSenderName();
    public function getMessage();
    public function getPlacement();
    public function getProductIds();
    public function getProductsById();
    public function replaceProducts(array $products);
}
