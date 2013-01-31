<?php

namespace View\Order\DeliveryCalc;

class Type {
    public $id;
    public $type;
    public $token;
    public $name;
    public $shortName;
    public $description;
    public $date;
    public $displayDate;
    public $interval;
    public $displayInterval;

    /* @var $shop Shop */
    public $shop;

    /* @var $items Item[] */
    public $items = [];


}