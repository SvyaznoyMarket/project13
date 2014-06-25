<?php

namespace EnterSite\Curl\Query;

class RetailRocketUrl {
    /**
     * scheme + host + path
     * @var string
     */
    public $prefix;
    /**
     * Например, Recomendation/CrossSellItemToItems
     * @var string
     */
    public $method;
    /**
     * Аккаунт в системе RetailRocket
     * @var string
     */
    public $account;
    /**
     * Например, идентификатор товара или категории
     * @var string
     */
    public $itemId;
    /**
     * после знака вопроса ?
     * @var array
     */
    public $query = [];

    /**
     * @return string
     */
    public function __toString() {
        return $this->prefix . $this->method. '/' . $this->account . '/' . $this->itemId  . ((bool)$this->query ? ('?' . http_build_query($this->query)) : '');
    }
}