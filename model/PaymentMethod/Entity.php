<?php

namespace Model\PaymentMethod;

class Entity {
    const CERTIFICATE_ID = 10;
    const QIWI_ID = 11;
    const WEBMONEY_ID = 12;

    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $description;
    /** @var bool */
    private $isCredit;
    /** @var bool */
    private $isOnline;
    /** @var bool */
    private $isCorporative;
    /** @var bool */
    private $payOnReceipt;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
        if (array_key_exists('is_credit', $data)) $this->setIsCredit($data['is_credit']);
        if (array_key_exists('is_online', $data)) $this->setIsOnline($data['is_online']);
        if (array_key_exists('is_corporative', $data)) $this->setIsCorporative($data['is_corporative']);
        if (array_key_exists('pay_on_receipt', $data)) $this->setPayOnReceipt($data['pay_on_receipt']);

    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = (string)$description;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param boolean $isCorporative
     */
    public function setIsCorporative($isCorporative) {
        $this->isCorporative = (bool)$isCorporative;
    }

    /**
     * @return boolean
     */
    public function getIsCorporative() {
        return $this->isCorporative;
    }

    /**
     * @param boolean $isCredit
     */
    public function setIsCredit($isCredit) {
        $this->isCredit = (bool)$isCredit;
    }

    /**
     * @return boolean
     */
    public function getIsCredit() {
        return $this->isCredit;
    }

    /**
     * @param boolean $isOnline
     */
    public function setIsOnline($isOnline) {
        $this->isOnline = (bool)$isOnline;
    }

    /**
     * @return boolean
     */
    public function getIsOnline() {
        return $this->isOnline;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isCertificate() {
        return self::CERTIFICATE_ID == $this->id;
    }

    /**
     * @return bool
     */
    public function isQiwi() {
        return self::QIWI_ID == $this->id;
    }

    /**
     * @return bool
     */
    public function isWebmoney() {
        return self::WEBMONEY_ID == $this->id;
    }

    /**
     * @param boolean $payOnReceipt
     */
    public function setPayOnReceipt($payOnReceipt) {
        $this->payOnReceipt = (bool)$payOnReceipt;
    }

    /**
     * @return boolean
     */
    public function getPayOnReceipt() {
        return $this->payOnReceipt;
    }

}