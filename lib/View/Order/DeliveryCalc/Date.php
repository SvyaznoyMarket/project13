<?php

namespace View\Order\DeliveryCalc;

class Date {
    public $value;
    public $dayOfWeek;
    public $day;
    public $timestamp;

    /* @var $intervals Interval[] */
    public $intervals = array();
}