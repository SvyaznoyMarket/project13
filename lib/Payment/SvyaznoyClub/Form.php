<?php

namespace Payment\SvyaznoyClub;

class Form {
    /** @var  int */
    protected $shopId;
    /** @var  int */
    protected $orderId;
    /** @var  float */
    protected $maxDiscount;
    /** @var  int */
    protected $totalCost;
    /** @var  string */
    protected $email;
    /** @var  string */
    protected $cardNumber;
    /** @var  string */
    protected $userTicket;
    /** @var  string */
    protected $signature;

    /**
     * @param array $data
     */
    public function fromArray(array $data) {
        if (array_key_exists('ShopId', $data)) $this->setShopId($data['ShopId']);
        if (array_key_exists('OrderId', $data)) $this->setOrderId($data['OrderId']);
        if (array_key_exists('MaxDiscount', $data)) $this->setMaxDiscount($data['MaxDiscount']);
        if (array_key_exists('TotalCost', $data)) $this->setTotalCost($data['TotalCost']);
        if (array_key_exists('Email', $data)) $this->setEmail($data['Email']);
        if (array_key_exists('CardNumber', $data)) $this->setCardNumber($data['CardNumber']);
        if (array_key_exists('UserTicket', $data)) $this->setUserTicket($data['UserTicket']);
        if (array_key_exists('Signature', $data)) $this->setSignature($data['Signature']);
    }

    /**
     * @param string $cardNumber
     */
    public function setCardNumber($cardNumber) {
        $this->cardNumber = $cardNumber;
    }

    /**
     * @return string
     */
    public function getCardNumber() {
        return $this->cardNumber;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param float $maxDiscount
     */
    public function setMaxDiscount($maxDiscount) {
        $this->maxDiscount = $maxDiscount;
    }

    /**
     * @return float
     */
    public function getMaxDiscount() {
        return $this->maxDiscount;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId) {
        $this->orderId = $orderId;
    }

    /**
     * @return int
     */
    public function getOrderId() {
        return $this->orderId;
    }

    /**
     * @param int $shopId
     */
    public function setShopId($shopId) {
        $this->shopId = $shopId;
    }

    /**
     * @return int
     */
    public function getShopId() {
        return $this->shopId;
    }

    /**
     * @param string $signature
     */
    public function setSignature($signature) {
        $this->signature = $signature;
    }

    /**
     * @return string
     */
    public function getSignature() {
        return $this->signature;
    }

    /**
     * @param int $totalCost
     */
    public function setTotalCost($totalCost) {
        $this->totalCost = $totalCost;
    }

    /**
     * @return int
     */
    public function getTotalCost() {
        return $this->totalCost;
    }

    /**
     * @param string $userTicket
     */
    public function setUserTicket($userTicket) {
        $this->userTicket = $userTicket;
    }

    /**
     * @return string
     */
    public function getUserTicket() {
        return $this->userTicket;
    }
} 