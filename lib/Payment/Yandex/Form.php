<?php

namespace Payment\Yandex;

class Form {
    /** @var string */
    public $url;
    /** @var string */
    public $shopId;
    /** @var string */
    public $scid;
    /** @var string */
    public $sum;
    /** @var string */
    public $customerNumber;
    /** @var string */
    public $shopArticleId;
    /** @var string */
    public $paymentType;
    /** @var string */
    public $orderNumber;
    /** @var string */
    public $cps_phone;
    /** @var string */
    public $cps_email;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        foreach ($data as $k => $v) {
            if (!property_exists($this, $k)) continue;

            $this->{$k} = $v;
        }
    }
}