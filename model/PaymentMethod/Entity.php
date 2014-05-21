<?php

namespace Model\PaymentMethod;

class Entity {
    const CASH_ID = 1;
    const CARD_ID = 2;
    const CREDIT_ID = 6;
    const CERTIFICATE_ID = 10;
    const WEBMONEY_ID = 11;
    const QIWI_ID = 12;
    const PAYPAL_ID = 13;

    const TYPE_NOW = 2;
    const TYPE_ON_RECEIPT = 1;
    const TYPE_ALL = 0;

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
    private $isAvailableToPickpoint = true;
    /** @var bool */
    private $isPersonal;
    /** @var bool */
    private $isLegal;

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
        if (array_key_exists('available_to_pickpoint', $data)) $this->setIsAvailableToPickpoint($data['available_to_pickpoint']);
        if (array_key_exists('is_personal', $data)) $this->setIsPersonal($data['is_personal']);
        if (array_key_exists('is_legal', $data)) $this->setIsLegal($data['is_legal']);
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
    public function isCash() {
        return self::CASH_ID == $this->id;
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
     * @return bool
     */
    public function isPaypal() {
        return self::PAYPAL_ID == $this->id;
    }

    /**
     * @return bool
     */
    public function getIsAvailableToPickpoint() {
        return $this->isAvailableToPickpoint;
    }

    /**
     * @param boo $val
     */
    public function setIsAvailableToPickpoint($val) {
        if ( null === $val) {
            // Если в ядре не определена (is null) доступность метода оплаты, то по дефолту ставим тру
            $ret = true;
        } else {
            $ret = $val;
        }
        $this->isAvailableToPickpoint = (bool)$ret;
    }

    /**
     * @param boolean $isLegal
     */
    public function setIsLegal($isLegal) {
        $this->isLegal = $isLegal;
    }

    /**
     * @return boolean
     */
    public function getIsLegal() {
        return $this->isLegal;
    }

    /**
     * @param boolean $isPersonal
     */
    public function setIsPersonal($isPersonal) {
        $this->isPersonal = $isPersonal;
    }

    /**
     * @return boolean
     */
    public function getIsPersonal() {
        return $this->isPersonal;
    }
}