<?php
namespace Session;

class CartProductLimitException extends \Exception {
    /** @var int */
    public $productLimit = 0;
}