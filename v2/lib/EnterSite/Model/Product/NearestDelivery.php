<?php

namespace EnterSite\Model\Product;

use EnterSite\Model;

class NearestDelivery {
    /** @var string */
    public $id;
    /** @var string */
    public $token;
    /** @var string */
    public $productId;
    /** @var string */
    public $price;
    /** @var Model\Shop[] */
    public $shopsById = [];
    /** @var \DateTime|null */
    public $deliveredAt;
}