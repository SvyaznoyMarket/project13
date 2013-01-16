<?php

namespace Payment\Psb;

class Form {
    /** @var string */
    private $amount;
    /** @var string */
    private $currency;
    /** @var string */
    private $order;
    /** @var string */
    private $desc;
    /** @var string */
    private $terminal;
    /** @var string */
    private $trtype;
    /** @var string */
    private $merchName;
    /** @var string */
    private $merchant;
    /** @var string */
    private $email;
    /** @var string */
    private $timestamp;
    /** @var string */
    private $nonce;
    /** @var string */
    private $backref;
    /** @var string */
    private $pSign;

    /**
     * @param array $data
     */
    public function fromArray(array $data) {
        if (array_key_exists('AMOUNT', $data)) $this->setAmount($data['AMOUNT']);
        if (array_key_exists('CURRENCY', $data)) $this->setCurrency($data['CURRENCY']);
        if (array_key_exists('ORDER', $data)) $this->setOrder($data['ORDER']);
        if (array_key_exists('DESC', $data)) $this->setDesc($data['DESC']);
        if (array_key_exists('TERMINAL', $data)) $this->setTerminal($data['TERMINAL']);
        if (array_key_exists('TRTYPE', $data)) $this->setTrtype($data['TRTYPE']);
        if (array_key_exists('MERCH_NAME', $data)) $this->setMerchName($data['MERCH_NAME']);
        if (array_key_exists('MERCHANT', $data)) $this->setMerchant($data['MERCHANT']);
        if (array_key_exists('EMAIL', $data)) $this->setEmail($data['EMAIL']);
        if (array_key_exists('TIMESTAMP', $data)) $this->setTimestamp($data['TIMESTAMP']);
        if (array_key_exists('NONCE', $data)) $this->setNonce($data['NONCE']);
        if (array_key_exists('BACKREF', $data)) $this->setBackref($data['BACKREF']);
        if (array_key_exists('P_SIGN', $data)) $this->setPSign($data['P_SIGN']);
    }

    /**
     * @param string $amount
     */
    public function setAmount($amount) {
        $this->amount = (string)$amount;
    }

    /**
     * @return string
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * @param string $backref
     */
    public function setBackref($backref) {
        $this->backref = (string)$backref;
    }

    /**
     * @return string
     */
    public function getBackref() {
        return $this->backref;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency) {
        $this->currency = (string)$currency;
    }

    /**
     * @return string
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * @param string $desc
     */
    public function setDesc($desc) {
        $this->desc = (string)$desc;
    }

    /**
     * @return string
     */
    public function getDesc() {
        return $this->desc;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = (string)$email;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $merchName
     */
    public function setMerchName($merchName) {
        $this->merchName = (string)$merchName;
    }

    /**
     * @return string
     */
    public function getMerchName() {
        return $this->merchName;
    }

    /**
     * @param string $merchant
     */
    public function setMerchant($merchant) {
        $this->merchant = (string)$merchant;
    }

    /**
     * @return string
     */
    public function getMerchant() {
        return $this->merchant;
    }

    /**
     * @param string $nonce
     */
    public function setNonce($nonce) {
        $this->nonce = (string)$nonce;
    }

    /**
     * @return string
     */
    public function getNonce() {
        return $this->nonce;
    }

    /**
     * @param string $order
     */
    public function setOrder($order) {
        $this->order = (string)$order;
    }

    /**
     * @return string
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * @param string $pSign
     */
    public function setPSign($pSign) {
        $this->pSign = (string)$pSign;
    }

    /**
     * @return string
     */
    public function getPSign() {
        return $this->pSign;
    }

    /**
     * @param string $terminal
     */
    public function setTerminal($terminal) {
        $this->terminal = (string)$terminal;
    }

    /**
     * @return string
     */
    public function getTerminal() {
        return $this->terminal;
    }

    /**
     * @param string $timestamp
     */
    public function setTimestamp($timestamp) {
        $this->timestamp = (string)$timestamp;
    }

    /**
     * @return string
     */
    public function getTimestamp() {
        return $this->timestamp;
    }

    /**
     * @param string $trtype
     */
    public function setTrtype($trtype) {
        $this->trtype = (string)$trtype;
    }

    /**
     * @return string
     */
    public function getTrtype() {
        return $this->trtype;
    }
    /** @var string */
}