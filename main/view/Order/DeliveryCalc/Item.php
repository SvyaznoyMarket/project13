<?php

namespace View\Order\DeliveryCalc;

class Item {
    const TYPE_PRODUCT = 'product';

    public $id;
    public $type; // товар или услуга
    public $token;
    public $stock;
    public $name;
    public $image;
    public $price;
    public $quantity;
    public $total;
    public $url;
    public $addUrl;
    public $deleteUrl;
    public $parent_category;
    public $article;
    public $category;

    /* @var $deliveries Delivery[] */
    public $deliveries = [];
}